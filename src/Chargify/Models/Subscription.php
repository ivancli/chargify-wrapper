<?php

namespace Invigor\Chargify\Models;

use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Traits\Curl;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:10 PM
 */
class Subscription
{
    use Curl;

    protected $id;
    protected $activated_at;
    protected $balance_in_cents;
    protected $cancel_at_end_of_period;
    protected $canceled_at;
    protected $cancellation_message;
    protected $coupon_code;
    protected $created_at;
    protected $current_period_started_at;
    protected $current_period_ends_at;
    protected $delayed_cancel_at;
    protected $expires_at;
    protected $next_assessment_at;
    protected $payment_type;
    protected $previous_state;
    protected $product_price_in_cents;
    protected $product_version_number;
    protected $signup_payment_id;
    protected $signup_revenue;
    protected $state;
    protected $total_revenue_in_cents;
    protected $trial_started_at;
    protected $trial_ended_at;
    protected $updated_at;
    protected $referral_code;
    protected $current_billing_amount_in_cents;
    protected $next_product_id;
    protected $cancellation_method;
    protected $payment_collection_method;
    protected $snap_day;

    protected $customer;
    protected $product;
    protected $credit_card;
    protected $bank_account;

    public function __construct($id = null)
    {
        if (!is_null($subscription = $this->getSubscription($id))) {
            foreach ($subscription as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    private function getSubscription($id)
    {
        $url = config('chargify.api_domain') . "subscriptions/$id.json";
        $subscription = $this->get($url);
        if (!is_null($subscription)) {
            return $subscription->subscription;
        } else {
            return null;
        }
    }

    public static function all()
    {
//        if (config('chargify.caching.enable') == true) {
//            return Cache::remember('chargify.subscriptions.all', config('chargify.caching.ttl'), function () {
//                return (new static)->_all();
//            });
//        } else {
            return (new static)->_all();
//        }
    }

    private function _all()
    {
        $url = config('chargify.api_domain') . "subscriptions.json";
        $subscriptions = (new static)->get($url);
        if (is_array($subscriptions)) {
            $subscriptions = array_pluck($subscriptions, 'subscription');
            $output = array();
            foreach ($subscriptions as $subscription) {
                $output[] = $this->_set($subscription);
//                $output[] = $subscription;
            }

            return $output;
        } else {
            return array();
        }
    }

    private function _set($input_subscription)
    {
        $start = microtime();

        //the following line is taking too much time
        $subscription = new static();
        dump($input_subscription);
//        foreach ($input_subscription as $key => $value) {
//            if (property_exists($subscription, $key)) {
//                $subscription->$key = $value;
//            }
//        }
        $end = microtime();
        dump($end - $start);

        return $subscription;
    }

    protected function bank_account()
    {

    }

    protected function credit_card()
    {

    }

    protected function customer()
    {

    }

    protected function product()
    {

    }
}