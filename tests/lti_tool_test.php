<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_dteach;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');

/**
 * What the plugin finishes on the tool Dynamic Registration leaves behind.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_dteach\lti_tool
 */
final class lti_tool_test extends \advanced_testcase {
    /**
     * Tool types are site configuration, so they need an admin to create them.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('serviceurl', 'https://api.dteach.net', 'local_dteach');
    }

    /**
     * Register a tool the way Dynamic Registration leaves it: pending, out of the
     * activity chooser, and launched in an embed.
     *
     * @param string $clientid client id the platform minted for the registration
     * @param string $toolurl launch URL the tool registered
     * @return int id of the created tool
     */
    private function create_pending_tool(
        string $clientid,
        string $toolurl = 'https://api.dteach.net/api/lti/launch/'
    ): int {
        $type = (object) [
            'name' => 'DTeach',
            'state' => LTI_TOOL_STATE_PENDING,
            'coursevisible' => LTI_COURSEVISIBLE_PRECONFIGURED,
            'clientid' => $clientid,
            'ltiversion' => LTI_VERSION_1P3,
        ];
        $config = (object) [
            'lti_toolurl' => $toolurl,
            'lti_launchcontainer' => LTI_LAUNCH_CONTAINER_EMBED,
        ];

        return lti_add_type($type, $config);
    }

    /**
     * Read back the three columns activation is about.
     *
     * @param int $typeid id of the tool
     * @return array state, coursevisible and launch container of the tool
     */
    private function tool_settings(int $typeid): array {
        global $DB;

        $record = $DB->get_record('lti_types', ['id' => $typeid], '*', MUST_EXIST);
        $config = lti_get_type_config($typeid);

        return [
            'state' => (int) $record->state,
            'coursevisible' => (int) $record->coursevisible,
            'launchcontainer' => (int) $config['launchcontainer'],
        ];
    }

    /**
     * A running plan puts DTeach where teachers can add it.
     */
    public function test_activate_offers_the_tool_in_the_activity_chooser(): void {
        $typeid = $this->create_pending_tool('client-one');

        $this->assertTrue(lti_tool::activate('client-one', true));

        $this->assertSame([
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'launchcontainer' => LTI_LAUNCH_CONTAINER_WINDOW,
        ], $this->tool_settings($typeid));
    }

    /**
     * A plan that ended takes DTeach out of the chooser, tool and activities intact.
     */
    public function test_activate_hides_the_tool_when_the_plan_is_over(): void {
        $typeid = $this->create_pending_tool('client-one');

        $this->assertTrue(lti_tool::activate('client-one', false));

        $this->assertSame([
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'coursevisible' => LTI_COURSEVISIBLE_NO,
            'launchcontainer' => LTI_LAUNCH_CONTAINER_WINDOW,
        ], $this->tool_settings($typeid));
    }

    /**
     * A plan renewed brings DTeach back on the next page load.
     */
    public function test_activate_returns_the_tool_to_the_chooser(): void {
        $typeid = $this->create_pending_tool('client-one');
        lti_tool::activate('client-one', false);

        lti_tool::activate('client-one', true);

        $this->assertSame(
            LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            $this->tool_settings($typeid)['coursevisible']
        );
    }

    /**
     * The connection page calls this on every load, so a settled tool stays as it is.
     */
    public function test_activate_is_idempotent(): void {
        $typeid = $this->create_pending_tool('client-one');
        lti_tool::activate('client-one', true);
        $settled = $this->tool_settings($typeid);

        $this->assertTrue(lti_tool::activate('client-one', true));

        $this->assertSame($settled, $this->tool_settings($typeid));
    }

    /**
     * Settings the admin made on the tool outlive every switch the plan causes.
     */
    public function test_activate_keeps_the_admin_settings(): void {
        $typeid = $this->create_pending_tool('client-one');
        lti_update_config((object) ['typeid' => $typeid, 'name' => 'forcessl', 'value' => 1]);

        lti_tool::activate('client-one', true);
        lti_tool::activate('client-one', false);

        $this->assertSame('1', (string) lti_get_type_config($typeid)['forcessl']);
    }

    /**
     * Course categories the admin restricted the tool to outlive every switch the plan causes.
     */
    public function test_activate_keeps_the_course_categories(): void {
        global $DB;

        if (!$DB->get_manager()->table_exists('lti_types_categories')) {
            $this->markTestSkipped('Tools are restricted to course categories since Moodle 4.3.');
        }
        $typeid = $this->create_pending_tool('client-one');
        $category = $this->getDataGenerator()->create_category();
        $DB->insert_record('lti_types_categories', ['typeid' => $typeid, 'categoryid' => $category->id]);

        lti_tool::activate('client-one', true);
        lti_tool::activate('client-one', false);

        $this->assertTrue($DB->record_exists('lti_types_categories', [
            'typeid' => $typeid,
            'categoryid' => $category->id,
        ]));
    }

    /**
     * The client id comes from the service, so a tool launching anywhere else is left alone.
     */
    public function test_activate_ignores_a_tool_of_another_domain(): void {
        $typeid = $this->create_pending_tool('client-one', 'https://tool.example.com/launch');

        $this->assertFalse(lti_tool::activate('client-one', true));

        $this->assertSame(LTI_TOOL_STATE_PENDING, $this->tool_settings($typeid)['state']);
    }

    /**
     * A site whose registration never happened has no tool to activate.
     */
    public function test_activate_ignores_an_unknown_client_id(): void {
        $this->create_pending_tool('client-one');

        $this->assertFalse(lti_tool::activate('client-two', true));
    }
}
