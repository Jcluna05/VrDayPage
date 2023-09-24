<?php

namespace Hurrytimer;

use http\Cookie;
use Hurrytimer\Utils\Helpers;

class EvergreenCampaign extends Campaign
{
    /**
     * @var CookieDetection
     */
    private $cookieDetection;

    /**
     * @var IPDetection
     */
    private $IPDetection;

    const RESET_FLAG = '_hurrytimer_reset_compaign_flag';

    public function __construct($id, $cookieDetection, $IPDetection)
    {
        parent::__construct($id);
        $this->IPDetection     = $IPDetection;
        $this->cookieDetection = $cookieDetection;
    }

    /**
     * Reset timer.
     *
     * @param string $scope
     */

    public function reset($scope = 'admin')
    {
        if ($scope === 'admin') {
            $this->IPDetection->forget($this->get_id(), Helpers::ip_address());
        } else {
            $this->IPDetection->forget($this->get_id());
            $this->resetBrowserCookie();
            $this->invalidate_reset_token();
        }
    }

    private function resetBrowserCookie()
    {
        $cookie_name = CookieDetection::cookieName($this->get_id());

        unset($_COOKIE[$cookie_name]);

        setcookie($cookie_name, '', time() - YEAR_IN_SECONDS);

        update_post_meta($this->get_id(), self::RESET_FLAG, time());
    }

     function resetAll($scope = 'admin')
    {
        if ($scope === 'admin') {
           $this->IPDetection->forgetAll(Helpers::ip_address());
        } else {
            $this->IPDetection->forgetAll();

            // Delete cookies
            $campaigns = Helpers::getCampaigns();

            foreach ($campaigns as $campaign) {

                $cookie_name = CookieDetection::cookieName($campaign->ID);

                if (isset($_COOKIE[$cookie_name])) {
                    unset($_COOKIE[$cookie_name]);
                }

                setcookie($cookie_name, '', time() - YEAR_IN_SECONDS);

                // Force cookie deletion on next request.
                update_post_meta($campaign->ID, self::RESET_FLAG, time());

            }
        }
    }

      /**
     * Returns client expiration time.
     *
     * @return int
     */

    public function get_cookie_reset_token(){

        // Get current reset token.
        return $this->cookieDetection->find_reset_token( $this->get_id() );

}

    public function get_ip_reset_token(){

        $result = $this->IPDetection->find_reset_token($this->get_id());

        if( $result ){
            return $result['reset_token'];
        }

        return false;

    }

    public function should_reset()
    {
        $new_reset_token = get_post_meta($this->get_id(), self::RESET_FLAG, true);

        if( ! $new_reset_token ){
            return false;
        }

        $cookie_reset_token = $this->get_cookie_reset_token();

        $ip_reset_token = $this->get_ip_reset_token();

        $user_reset_token = max($ip_reset_token, $cookie_reset_token);

        $this->update_ip_reset_token($user_reset_token);

        return ! $user_reset_token || ( $new_reset_token > $user_reset_token);
    }

    function update_ip_reset_token($token){

        $result = $this->IPDetection->find_reset_token($this->get_id());

        if( ! empty($result) ){
            $this->IPDetection->update_reset_token($result['id'], $token);
        }
    }

    function invalidate_reset_token(){
        $cookie_name = CookieDetection::cookieName($this->get_id()) . '_reset_token';
        setcookie($cookie_name, '', time() - YEAR_IN_SECONDS);
        unset($_COOKIE[$cookie_name]);

    }


    /**
     * Returns client expiration time.
     *
     * @return int
     */
    public function getEndDate()
    {
        // Get expire timestamp if cookie exists.
        $cookie_timestamp = $this->cookieDetection->find($this->get_id());

        $result = $this->IPDetection->find($this->get_id());

        $ip_timestamp = ! empty($result) ? $result['client_expires_at'] : false;

        $timestamp = max($cookie_timestamp, $ip_timestamp);

        if( empty($timestamp) ){
            return null;
        }

        if ($result) {
            // Update IP expiration timestamp.
            $this->IPDetection->update($result['id'], $timestamp);
        } else {
            // We create an IP entry.
            $this->IPDetection->create($this->get_id(), $timestamp);
        }

        $this->IPDetection->update_reset_token($result['id'], $this->reseting());

        return $timestamp;
    }

    function setEndDate($timestamp)
    {
        setcookie(CookieDetection::cookieName($this->get_id()), $timestamp,
            time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);

        $result = $this->IPDetection->find($this->get_id());

        if ($result) {
            // Update IP expiration timestamp.
            $this->IPDetection->update($result['id'], $timestamp);
        } else {
            // We create an IP entry.
            $this->IPDetection->create($this->get_id(), $timestamp);
        }

        return $this->getEndDate();
    }

    /**
     * Returns true to force reset timer.
     *
     * @return bool
     */
    public function reseting()
    {
     return get_post_meta($this->get_id(), self::RESET_FLAG, true);

    }

    /**
     * Returns given timer's cookie name.
     *
     * @return string
     */
    public function cookieName()
    {
        return CookieDetection::cookieName($this->get_id());
    }
}
