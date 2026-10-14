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
 * A transport that answers from a canned response and records what it was asked.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mock_curl extends \curl {
    /** @var array Calls made so far, each with its method, url, body and headers. */
    public $calls = [];

    /** @var int Status code every answer carries. */
    private $httpcode;

    /** @var string Body every answer carries. */
    private $body;

    /**
     * Build a transport answering the same thing every time.
     *
     * @param int $httpcode status code to answer with
     * @param string $body body to answer with
     */
    public function __construct(int $httpcode, string $body) {
        parent::__construct();
        $this->httpcode = $httpcode;
        $this->body = $body;
    }

    /**
     * Answer a GET without leaving the process.
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @return string the canned body
     */
    public function get($url, $params = [], $options = []) {
        return $this->answer('GET', $url, '');
    }

    /**
     * Answer a POST without leaving the process.
     *
     * @param string $url
     * @param array|string $params
     * @param array $options
     * @return string the canned body
     */
    public function post($url, $params = '', $options = []) {
        return $this->answer('POST', $url, (string) $params);
    }

    /**
     * Record one call and answer it.
     *
     * @param string $method
     * @param string $url
     * @param string $params request body, empty on a GET
     * @return string the canned body
     */
    private function answer(string $method, string $url, string $params): string {
        $this->calls[] = [
            'method' => $method,
            'url' => $url,
            'params' => $params,
            'header' => $this->header,
        ];
        $this->info = ['http_code' => $this->httpcode];
        return $this->body;
    }
}
