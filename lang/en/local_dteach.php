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
 * English strings.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activate'] = 'Activate';
$string['active'] = 'DTeach is connected to this site.';
$string['connection'] = 'Connection';
$string['connectstep1'] = 'The address of this site is sent to DTeach with your licence key. DTeach answers with a registration link, and nothing else is sent.';
$string['connectstep2'] = 'Moodle registers with DTeach, and DTeach is added to the activity chooser of every course.';
$string['connectstep3'] = 'Teachers add DTeach from any course, and their students open it with their Moodle account.';
$string['connectsteps'] = 'What happens when you activate';
$string['contactus'] = 'Contact us';
$string['endson'] = 'Ends on';
$string['errorlicencealreadyclaimed'] = 'This licence key already activates another site. One key opens one site.';
$string['errorlicencenotactive'] = 'This licence is no longer active. Contact us to renew it, then activate this site again.';
$string['errorsitealreadyconnected'] = 'This site is already connected to DTeach.';
$string['errorunknownlicencekey'] = 'This licence key is not recognised. Check the key DTeach sent you.';
$string['expired'] = 'Your plan has ended, and teachers can no longer open DTeach. Contact us to renew it. This site keeps its licence key.';
$string['finishsetup'] = 'Finish setup';
$string['licencekey'] = 'Licence key';
$string['licencekeyhelp'] = 'The key DTeach sent you by email. It activates this site, and no other.';
$string['licencekeyrequired'] = 'Enter the licence key DTeach sent you.';
$string['notconnected'] = 'This site is not connected yet. Paste your licence key to add DTeach to the activity chooser.';
$string['plan'] = 'Plan';
$string['pluginname'] = 'DTeach';
$string['privacy:metadata:dteach'] = 'This plugin sends the address of this site and its licence key to DTeach, to register the site and read its plan. It sends no personal data: DTeach activities are launched by the External tool (LTI) module, which declares what it transmits.';
$string['privacy:metadata:dteach:licencekey'] = 'The licence key issued for this site, used to identify the plan it holds.';
$string['privacy:metadata:dteach:siteurl'] = 'The address of this Moodle site, used to identify the plan it holds.';
$string['privacy:metadata:nullproviderreason'] = 'This plugin stores only site-level settings: the licence key issued for this site, the token identifying it to DTeach, and its registration address. It stores no data about any user.';
$string['reconnect'] = 'Reconnect';
$string['registrationpending'] = 'Your licence key is activated. Finish setup to register this site and put DTeach in the activity chooser.';
$string['servicefailed'] = 'DTeach could not be reached: {$a}';
$string['serviceurl'] = 'Service URL';
$string['serviceurl_desc'] = 'Base URL of the DTeach API. Leave the default unless you are testing against another environment.';
$string['settings'] = 'Settings';
$string['site'] = 'Site';
$string['stateactive'] = 'Active';
$string['stateexpired'] = 'Expired';
$string['statenotconnected'] = 'Not connected';
$string['tokenrejected'] = 'The registration link of this site had expired. A new one was issued with its licence key: finish setup below.';
$string['toolmissing'] = 'DTeach is connected, but its tool is no longer on this site. Reconnect to add it back: teachers keep their DTeach courses and can add them to Moodle again.';
$string['toolready'] = 'DTeach is active and available in the activity chooser.';
