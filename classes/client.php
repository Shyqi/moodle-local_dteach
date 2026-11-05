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
 * Client over the dteach service plugin endpoints.
 *
 * @package    local_dteach
 * @copyright  2026 dteach <contact@dteach.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client {
    /** @var int Seconds to wait for the service before giving up. */
    private const TIMEOUT = 10;

    /** @var string Service base URL, without its trailing slash. */
    private $serviceurl;

    /** @var \curl Transport every call goes through. */
    private $curl;

    /**
     * Build a client from the plugin settings.
     *
     * @param \curl|null $curl transport to use, a fresh one when not given
     */
    public function __construct(?\curl $curl = null) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $this->serviceurl = rtrim((string) get_config('local_dteach', 'serviceurl'), '/');
        $this->curl = $curl ?? new \curl();
    }

    /**
     * Spend the licence key on this site, claiming the token it keeps for every later call.
     *
     * @param string $siteurl wwwroot of this Moodle
     * @param string $licencekey key dteach sent to the buyer
     * @return array the issued invite, including its registration_url
     */
    public function issue_token(string $siteurl, string $licencekey): array {
        return $this->request('POST', '/api/lti/dteach/invites/', [
            'site_url' => $siteurl,
            'licence_key' => $licencekey,
        ], null);
    }

    /**
     * Connect this site for free, with no key: the service only needs a way to reach its admin.
     *
     * @param string $siteurl wwwroot of this Moodle
     * @param string $contactemail where DTeach reaches the admin who connected the site
     * @return array the issued invite, including its registration_url
     */
    public function connect_free(string $siteurl, string $contactemail): array {
        return $this->request('POST', '/api/lti/dteach/invites/', [
            'site_url' => $siteurl,
            'contact_email' => $contactemail,
        ], null);
    }

    /**
     * Spend a licence key on a site that is already connected, typically one on the free plan.
     *
     * @param string $token
     * @param string $licencekey key dteach sent to the buyer
     * @return array the connection state, now on the plan the key holds
     */
    public function claim_licence(string $token, string $licencekey): array {
        return $this->request('POST', '/api/lti/dteach/licence/', [
            'licence_key' => $licencekey,
        ], $token);
    }

    /**
     * Get back the token of a site that is already connected, on the word of the site's LMS.
     *
     * @param string $assertion JWT signed with this site's LTI platform key
     * @return array the site's invite, including its token
     */
    public function recover(string $assertion): array {
        return $this->request('POST', '/api/lti/dteach/recover/', [
            'assertion' => $assertion,
        ], null);
    }

    /**
     * Connection and plan state behind a token.
     *
     * @param string $token
     * @return array
     */
    public function status(string $token): array {
        return $this->request('GET', '/api/lti/dteach/status/', null, $token);
    }

    /**
     * Reopen the registration link of a site whose tool went missing from the LMS.
     *
     * @param string $token
     * @return array the connection state, including the reopened registration_url
     */
    public function reconnect(string $token): array {
        return $this->request('POST', '/api/lti/dteach/reconnect/', [], $token);
    }

    /**
     * Issue one call and decode its JSON body.
     *
     * @param string $method
     * @param string $path
     * @param array|null $body encoded as JSON when present
     * @param string|null $token sent as a bearer token when present
     * @return array
     * @throws service_exception when the service answers anything but a 2xx JSON object
     */
    private function request(string $method, string $path, ?array $body, ?string $token): array {
        $headers = ['Accept: application/json'];
        if ($token !== null) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        // The same transport serves both calls of a page load, and headers pile up.
        $this->curl->resetHeader();
        $this->curl->setHeader($headers);
        $options = ['CURLOPT_TIMEOUT' => self::TIMEOUT, 'CURLOPT_CONNECTTIMEOUT' => self::TIMEOUT];
        $url = $this->serviceurl . $path;
        if ($method === 'GET') {
            $response = $this->curl->get($url, [], $options);
        } else {
            $response = $this->curl->post($url, json_encode($body), $options);
        }

        $code = (int) ($this->curl->get_info()['http_code'] ?? 0);
        $decoded = json_decode((string) $response, true);
        if ($code < 200 || $code >= 300 || !is_array($decoded)) {
            $servicecode = '';
            $detail = (string) $this->curl->error;
            if (is_array($decoded)) {
                $servicecode = is_string($decoded['code'] ?? null) ? $decoded['code'] : '';
                $detail = is_string($decoded['detail'] ?? null) ? $decoded['detail'] : '';
            }
            throw new service_exception($servicecode, $detail ?: (string) $code);
        }
        return $decoded;
    }
}
