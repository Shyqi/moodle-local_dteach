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
 * A call the dteach service refused, carrying the code it answered with.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class service_exception extends \moodle_exception {
    /** @var array Refusals the admin can act on, and the string that explains each of them. */
    private const KNOWN = [
        'unknown_licence_key' => 'errorunknownlicencekey',
        'licence_already_claimed' => 'errorlicencealreadyclaimed',
        'licence_not_active' => 'errorlicencenotactive',
        'site_already_connected' => 'errorsitealreadyconnected',
        'contact_email_required' => 'errorcontactemailrequired',
    ];

    /** @var string Machine-readable code from the service, empty when it sent none. */
    public $servicecode;

    /**
     * Build the exception from what the service answered with.
     *
     * @param string $servicecode code the service answered with
     * @param string $detail human-readable reason, used when the code means nothing to us
     */
    public function __construct(string $servicecode, string $detail) {
        $this->servicecode = $servicecode;
        if (isset(self::KNOWN[$servicecode])) {
            parent::__construct(self::KNOWN[$servicecode], 'local_dteach');
            return;
        }
        parent::__construct('servicefailed', 'local_dteach', '', $detail);
    }
}
