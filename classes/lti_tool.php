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

/**
 * Finishes what Dynamic Registration leaves undone on the created tool.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lti_tool {
    /**
     * Activate the registered tool, offering it in the activity chooser while the plan runs.
     *
     * Dynamic Registration always leaves the tool pending, hardcodes `coursevisible` to
     * preconfigured and the launch container to an embed, ignoring what the tool asked
     * for (see mod/lti/classes/local/ltiopenid/registration_helper.php). Idempotent, so
     * the connection page can call it on every load.
     *
     * The client id is what names our tool: dteach serves several products from one
     * launch URL, so a site running more than one of them has several tools sharing a
     * base URL, told apart only by the client id the platform minted per registration.
     *
     * @param string $clientid client id of the registration this site owns
     * @param bool $available whether the plan still allows new activities
     * @return bool whether the tool exists and is now ready to use
     */
    public static function activate(string $clientid, bool $available): bool {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $record = $DB->get_record('lti_types', ['clientid' => $clientid]);
        if ($record === false) {
            return false;
        }

        // Hiding the tool stops teachers adding an activity they could not open anyway;
        // activities already in a course stay, and the service refuses their launch.
        $coursevisible = $available ? LTI_COURSEVISIBLE_ACTIVITYCHOOSER : LTI_COURSEVISIBLE_NO;

        // Not lti_update_type(): it wipes what it is not given, course categories and forcessl.
        if ((int) $record->state !== LTI_TOOL_STATE_CONFIGURED || (int) $record->coursevisible !== $coursevisible) {
            $DB->update_record('lti_types', (object) [
                'id' => $record->id,
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'coursevisible' => $coursevisible,
                'timemodified' => time(),
            ]);
        }
        if ((int) (lti_get_type_config($record->id)['launchcontainer'] ?? 0) !== LTI_LAUNCH_CONTAINER_WINDOW) {
            lti_update_config((object) [
                'typeid' => $record->id,
                'name' => 'launchcontainer',
                'value' => LTI_LAUNCH_CONTAINER_WINDOW,
            ]);
        }

        return true;
    }
}
