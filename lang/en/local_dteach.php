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
$string['connectfree'] = 'Connect for free';
$string['connection'] = 'Connection';
$string['connectstep1'] = 'The address of this site is sent to DTeach, with your email address or your licence key. DTeach answers with a registration link, and nothing else is sent.';
$string['connectstep2'] = 'Moodle registers with DTeach, and DTeach is added to the activity chooser of every course.';
$string['connectstep3'] = 'Teachers add DTeach from any course, and their students open it with their Moodle account.';
$string['connectsteps'] = 'What happens when you connect';
$string['contactemail'] = 'Your email address';
$string['contactemailhelp'] = 'Where DTeach writes about this connection, and nothing else. It is sent to DTeach, not stored on this site.';
$string['contactemailrequired'] = 'Enter an email address to connect this site for free.';
$string['contactus'] = 'Contact us';
$string['endson'] = 'Ends on';
$string['errorcontactemailrequired'] = 'Connecting for free needs an email address. Enter one, or paste a licence key instead.';
$string['errorlicencealreadyclaimed'] = 'This licence key already activates another site. One key opens one site.';
$string['errorlicencenotactive'] = 'This licence is no longer active. Contact us to renew it, then activate this site again.';
$string['errorsitealreadyconnected'] = 'This site is already connected to DTeach.';
$string['errorunknownlicencekey'] = 'This licence key is not recognised. Check the key DTeach sent you.';
$string['expired'] = 'Your plan has ended, and teachers can no longer open DTeach. Contact us to renew it. This site keeps its licence key.';
$string['finishsetup'] = 'Finish setup';
$string['freefull'] = 'All {$a->max} free seats are taken, and members past them cannot open DTeach. Contact us to open more seats.';
$string['freeseats'] = 'DTeach is free on this site for up to {$a->max} members, and {$a->count} have joined. Members past {$a->max} cannot open DTeach.';
$string['havekey'] = 'Have a licence key?';
$string['licencekey'] = 'Licence key';
$string['licencekeyhelp'] = 'The key DTeach sent you by email, for more members and the AI assistant. It activates this site, and no other.';
$string['licencekeyrequired'] = 'Enter the licence key DTeach sent you.';
$string['members'] = 'Members';
$string['notconnected'] = 'This site is not connected yet. Connect for free to add DTeach to the activity chooser: up to 20 members, no licence key needed.';
$string['plan'] = 'Plan';
$string['pluginname'] = 'DTeach';
$string['privacy:metadata:dteach'] = 'This plugin sends the address of this site and its licence key to DTeach, to register the site and read its plan. It sends no personal data: DTeach activities are launched by the External tool (LTI) module, which declares what it transmits.';
$string['privacy:metadata:dteach:licencekey'] = 'The licence key issued for this site, used to identify the plan it holds.';
$string['privacy:metadata:dteach:siteurl'] = 'The address of this Moodle site, used to identify the plan it holds.';
$string['privacy:metadata:nullproviderreason'] = 'This plugin stores only site-level settings: the licence key issued for this site, the token identifying it to DTeach, and its registration address. It stores no data about any user.';
$string['reconnect'] = 'Reconnect';
$string['registrationpending'] = 'This site has its registration link. Finish setup to register it and put DTeach in the activity chooser.';
$string['seats'] = '{$a->count} / {$a->max}';
$string['servicefailed'] = 'DTeach could not be reached: {$a}';
$string['serviceurl'] = 'Service URL';
$string['serviceurl_desc'] = 'Base URL of the DTeach API. Leave the default unless you are testing against another environment.';
$string['settings'] = 'Settings';
$string['site'] = 'Site';
$string['stateactive'] = 'Active';
$string['stateexpired'] = 'Expired';
$string['statefree'] = 'Free';
$string['statenotconnected'] = 'Not connected';
$string['statetrial'] = 'Trial';
$string['tokenrejected'] = 'The registration link of this site had expired. A new one was issued with its licence key: finish setup below.';
$string['tokenrejectedfree'] = 'The registration link of this site had expired. Connect it for free again below.';
$string['toolmissing'] = 'DTeach is connected, but its tool is no longer on this site. Reconnect to add it back: teachers keep their DTeach courses and can add them to Moodle again.';
$string['toolready'] = 'DTeach is active and available in the activity chooser.';
$string['trialdaysleft'] = 'Every feature and the AI assistant, {$a} days left. DTeach then stays free on this site for up to 20 members.';
