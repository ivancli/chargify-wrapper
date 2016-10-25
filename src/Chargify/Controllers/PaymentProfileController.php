<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/25/2016
 * Time: 1:26 PM
 */

namespace Invigor\Chargify\Controllers;


use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\PaymentProfile;
use Invigor\Chargify\Traits\Curl;

class PaymentProfileController
{
    use Curl;

    public function create()
    {

    }

    /**
     * load a payment profile by payment profile id
     *
     * @param $payment_profile_id
     * @return PaymentProfile|null
     */
    public function get($payment_profile_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.payment_profiles.{$payment_profile_id}", config('chargify.caching.ttl'), function () use ($payment_profile_id) {
                return $this->__get($payment_profile_id);
            });
        } else {
            return $this->__get($payment_profile_id);
        }
    }

    /**
     * @param $payment_profile_id
     * @return PaymentProfile|null
     */
    private function __get($payment_profile_id)
    {
        $url = config('chargify.api_domain') . "payment_profiles/{$payment_profile_id}.json";
        $paymentProfile = $this->_get($url);
        if (!is_null($paymentProfile)) {
            $paymentProfile = $paymentProfile->payment_profile;
            $output = $this->__assign($paymentProfile);
            return $output;
        } else {
            return $paymentProfile;
        }
    }

    /**
     * @param $input_payment_profile
     * @return PaymentProfile
     */
    private function __assign($input_payment_profile)
    {
        $paymentProfile = new PaymentProfile;
        foreach ($input_payment_profile as $key => $value) {
            if (property_exists($paymentProfile, $key)) {
                $paymentProfile->$key = $value;
            }
        }
        return $paymentProfile;
    }
}