<?php

namespace Invigor\Chargify\Models;

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