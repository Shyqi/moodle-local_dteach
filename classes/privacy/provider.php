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

namespace local_dteach\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\null_provider;
use core_privacy\local\metadata\provider as metadata_provider;

/**
 * What this plugin sends to the dteach service, and why it stores nothing itself.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements metadata_provider, null_provider {
    /**
     * Describe the one call this plugin makes to the dteach service.
     *
     * @param collection $collection metadata collection to add to
     * @return collection the collection, with this plugin's external link added
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location_link('dteach', [
            'siteurl' => 'privacy:metadata:dteach:siteurl',
            'licencekey' => 'privacy:metadata:dteach:licencekey',
        ], 'privacy:metadata:dteach');

        return $collection;
    }

    /**
     * Why this plugin holds no personal data of its own.
     *
     * @return string identifier of the string explaining the absence of stored data
     */
    public static function get_reason(): string {
        return 'privacy:metadata:nullproviderreason';
    }
}
