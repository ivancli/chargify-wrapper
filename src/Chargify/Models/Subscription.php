<?php

namespace Invigor\Chargify\Models;

use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Controllers\CustomerController;
use Invigor\Chargify\Controllers\ProductController;
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

    public $id;
    public $activated_at;
    public $balance_in_cents;
    public $cancel_at_end_of_period;
    public $canceled_at;
    public $cancellation_message;
    public $coupon_code;
    public $created_at;
    public $current_period_started_at;
    public $current_period_ends_at;
    public $delayed_cancel_at;
    public $expires_at;
    public $next_assessment_at;
    public $payment_type;
    public $previous_state;
    public $product_price_in_cents;
    public $product_version_number;
    public $signup_payment_id;
    public $signup_revenue;
    public $state;
    public $total_revenue_in_cents;
    public $trial_started_at;
    public $trial_ended_at;
    public $updated_at;
    public $referral_code;
    public $current_billing_amount_in_cents;
    public $next_product_id;
    public $cancellation_method;
    public $payment_collection_method;
    public $snap_day;

    public $customer_id;
    public $product_id;
    public $credit_card_id;
    public $bank_account_id;

    private $customerController;
    private $productController;

    public function __construct()
    {
        $this->customerController = new CustomerController;
        $this->productController = new ProductController;
    }

    public function bank_account()
    {

    }

    public function credit_card()
    {

    }

    public function customer()
    {
        return $this->customerController->get($this->customer_id);
    }

    public function product()
    {
        return $this->productController->get($this->product_id);
    }

    public function save()
    {
        $url = config('chargify.api_domain') . "subscriptions/{$this->id}.json";
        $data = new \stdClass();
        $data->subscription = $this;
        $subscription = $this->_put($url, json_encode($data));
        if (!is_null($subscription)) {
            $subscription = $subscription->subscription;
            return $this;
        } else {
            return null;
        }
    }

    public function cancel()
    {

    }
}