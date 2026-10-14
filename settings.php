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

/**
 * Admin tree entries.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category(
        'local_dteach',
        new lang_string('pluginname', 'local_dteach')
    ));

    $ADMIN->add('local_dteach', new admin_externalpage(
        'local_dteach_connect',
        new lang_string('connection', 'local_dteach'),
        new moodle_url('/local/dteach/connect.php'),
        'moodle/site:config'
    ));

    $settings = new admin_settingpage(
        'local_dteach_settings',
        new lang_string('settings', 'local_dteach')
    );
    $settings->add(new admin_setting_configtext(
        'local_dteach/serviceurl',
        new lang_string('serviceurl', 'local_dteach'),
        new lang_string('serviceurl_desc', 'local_dteach'),
        'https://api.dteach.net',
        PARAM_URL
    ));
    $ADMIN->add('local_dteach', $settings);
}
