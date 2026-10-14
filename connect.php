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
 * Connection state of this site: the licence key form, the pending registration, or the plan.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_dteach\client;
use local_dteach\lti_tool;
use local_dteach\service_exception;

/** Component name, as every get_config() and get_string() call on this page needs it. */
const LOCAL_DTEACH = 'local_dteach';

/** Where the admin writes to renew a plan or ask for a key. */
const LOCAL_DTEACH_CONTACT = 'contact@dteach.net';

admin_externalpage_setup('local_dteach_connect');

$action = optional_param('action', '', PARAM_ALPHA);
$pageurl = new moodle_url('/local/dteach/connect.php');
$client = new client();
$token = (string) get_config(LOCAL_DTEACH, 'token');
$licencekey = (string) get_config(LOCAL_DTEACH, 'licencekey');
$pendingkey = '';
$reissued = false;
$error = '';
$warning = '';

if ($action !== '') {
    require_sesskey();
}

if ($action === 'register') {
    $stored = (string) get_config(LOCAL_DTEACH, 'registrationurl');
    if ($stored !== '') {
        redirect(new moodle_url('/mod/lti/startltiadvregistration.php', [
            'url' => $stored,
            'sesskey' => sesskey(),
        ]));
    }
}

if ($action === 'reconnect' && $token !== '') {
    try {
        $reopened = $client->reconnect($token);
        redirect(new moodle_url('/mod/lti/startltiadvregistration.php', [
            'url' => $reopened['registration_url'],
            'sesskey' => sesskey(),
        ]));
    } catch (service_exception $e) {
        $error = $e->getMessage();
    }
}

if ($action === 'activate') {
    $pendingkey = optional_param('licencekey', '', PARAM_ALPHANUM);
    if ($pendingkey === '') {
        $error = get_string('licencekeyrequired', LOCAL_DTEACH);
    }
}

$status = null;

if ($pendingkey === '' && $token !== '') {
    try {
        $status = $client->status($token);
    } catch (service_exception $e) {
        if ($e->servicecode !== 'invalid_token') {
            throw $e;
        }
        // A token issued but never registered is purged after a day. The key belongs to this
        // site for good, so spend it again rather than ask the admin to paste it a second time.
        unset_config('token', LOCAL_DTEACH);
        unset_config('registrationurl', LOCAL_DTEACH);
        $pendingkey = $licencekey;
        $reissued = true;
    }
}

if ($pendingkey !== '') {
    try {
        $invite = $client->issue_token($CFG->wwwroot, $pendingkey);
        set_config('licencekey', $pendingkey, LOCAL_DTEACH);
        set_config('token', $invite['token'], LOCAL_DTEACH);
        set_config('registrationurl', $invite['registration_url'], LOCAL_DTEACH);
        if ($reissued) {
            $warning = get_string('tokenrejected', LOCAL_DTEACH);
        }
        // A reinstalled plugin gets back the token of a site that is already registered.
        $status = $client->status($invite['token']);
        if ($action === 'activate' && empty($status['connected'])) {
            redirect(new moodle_url('/mod/lti/startltiadvregistration.php', [
                'url' => $invite['registration_url'],
                'sesskey' => sesskey(),
            ]));
        }
    } catch (service_exception $e) {
        $error = $e->getMessage();
    }
}

$registrationurl = (string) get_config(LOCAL_DTEACH, 'registrationurl');

$state = $status === null ? 'not_connected' : (string) ($status['state'] ?? 'not_connected');
$toolfound = false;
if (!empty($status['connected'])) {
    $toolfound = lti_tool::activate((string) ($status['client_id'] ?? ''), $state !== 'expired');
}
$toolmissing = !empty($status['connected']) && !$toolfound;

$endsat = $status === null ? null : ($status['plan']['ends_at'] ?? null);
$end = empty($endsat) ? false : strtotime($endsat);

// The service names a running plan 'trial' or 'paid'; both open the tool.
$active = $state === 'trial' || $state === 'paid';

if ($state === 'expired') {
    $statelabel = get_string('stateexpired', LOCAL_DTEACH);
    $statevariant = 'warning';
    $summary = get_string('expired', LOCAL_DTEACH);
} else if ($active) {
    $statelabel = get_string('stateactive', LOCAL_DTEACH);
    $statevariant = 'success';
    $summary = get_string($toolmissing ? 'toolmissing' : 'active', LOCAL_DTEACH);
} else {
    $statelabel = get_string('statenotconnected', LOCAL_DTEACH);
    $statevariant = 'secondary';
    $summary = $registrationurl === ''
        ? get_string('notconnected', LOCAL_DTEACH)
        : get_string('registrationpending', LOCAL_DTEACH);
}
$fresh = $state === 'not_connected' && $registrationurl === '';

$details = [];
if (!empty($status['plan']['name'])) {
    $details[] = [
        'label' => get_string('plan', LOCAL_DTEACH),
        'value' => $status['plan']['name'],
    ];
}
if ($end !== false) {
    $details[] = [
        'label' => get_string('endson', LOCAL_DTEACH),
        'value' => userdate($end, get_string('strftimedaydate')),
    ];
}
$details[] = [
    'label' => get_string('site', LOCAL_DTEACH),
    'value' => $status['site_url'] ?? $CFG->wwwroot,
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('connection', LOCAL_DTEACH));
if ($warning !== '') {
    echo $OUTPUT->notification($warning, \core\output\notification::NOTIFY_WARNING);
}
if ($error !== '') {
    echo $OUTPUT->notification(s($error), \core\output\notification::NOTIFY_ERROR);
}

// Both spacing sets are emitted so the row breathes on Bootstrap 4 (4.2) and 5 (5.0) alike.
$actions = '';
if ($state === 'not_connected' && $registrationurl !== '') {
    $actions .= html_writer::div($OUTPUT->render(new single_button(
        new moodle_url($pageurl, ['action' => 'register', 'sesskey' => sesskey()]),
        get_string('finishsetup', LOCAL_DTEACH),
        'post',
        single_button::BUTTON_PRIMARY
    )), 'mr-2 me-2');
}
if ($toolmissing) {
    $actions .= html_writer::div($OUTPUT->render(new single_button(
        new moodle_url($pageurl, ['action' => 'reconnect', 'sesskey' => sesskey()]),
        get_string('reconnect', LOCAL_DTEACH),
        'post',
        single_button::BUTTON_PRIMARY
    )), 'mr-2 me-2');
}
$actions .= html_writer::link(
    'mailto:' . LOCAL_DTEACH_CONTACT,
    get_string('contactus', LOCAL_DTEACH),
    ['class' => 'btn ' . ($state === 'expired' ? 'btn-primary' : 'btn-link')]
);

echo $OUTPUT->render_from_template('local_dteach/connect', [
    'statelabel' => $statelabel,
    'statevariant' => $statevariant,
    'summary' => $summary,
    'details' => $details,
    'toolready' => $active && $toolfound ? get_string('toolready', LOCAL_DTEACH) : '',
    'keyform' => $fresh ? [
        'formurl' => $pageurl->out(false),
        'sesskey' => sesskey(),
        'label' => get_string('licencekey', LOCAL_DTEACH),
        'help' => get_string('licencekeyhelp', LOCAL_DTEACH),
        'submit' => get_string('activate', LOCAL_DTEACH),
    ] : false,
    'connectsteps' => $fresh ? get_string('connectsteps', LOCAL_DTEACH) : '',
    'steps' => $fresh ? [
        get_string('connectstep1', LOCAL_DTEACH),
        get_string('connectstep2', LOCAL_DTEACH),
        get_string('connectstep3', LOCAL_DTEACH),
    ] : [],
    'actions' => $actions,
]);
echo $OUTPUT->footer();
