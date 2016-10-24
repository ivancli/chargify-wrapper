<?php
namespace Invigor\Chargify\Controllers;

use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\Customer;
use Invigor\Chargify\Models\Subscription;
use Invigor\Chargify\Traits\Curl;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/24/2016
 * Time: 9:33 AM
 */
class SubscriptionController
{
    use Curl;

    public function __construct()
    {

    }

    public function getSubscription($id)
    {
        $url = config('chargify.api_domain') . "subscriptions/$id.json";
        $subscription = $this->_get($url);
        if (!is_null($subscription)) {
            return $subscription->subscription;
        } else {
            return null;
        }
    }

    public function all()
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember('chargify.subscriptions.all', config('chargify.caching.ttl'), function () {
                return $this->__all();
            });
        } else {
            return $this->__all();
        }
    }

    public function get($id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.subscriptions.{$id}", config('chargify.caching.ttl'), function () use ($id) {
                return $this->__get($id);
            });
        } else {
            return $this->__get($id);
        }
    }

    private function __all()
    {
        $url = config('chargify.api_domain') . "subscriptions.json";
        $subscriptions = $this->_get($url);
        if (is_array($subscriptions)) {
            $subscriptions = array_pluck($subscriptions, 'subscription');
            $output = array();
            foreach ($subscriptions as $subscription) {
                $output[] = $this->__assign($subscription);
            }
            return $output;
        } else {
            return array();
        }
    }

    private function __get($id)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$id}.json";
        $subscription = $this->_get($url);
        if (!is_null($subscription)) {
            $subscription = $subscription->subscription;
            $output = $this->__assign($subscription);
            return $output;
        } else {
            return null;
        }
    }

    private function __assign($input_subscription)
    {
        $subscription = new Subscription;
        foreach ($input_subscription as $key => $value) {
            switch ($key) {
                case "customer":
                    if (isset($value->id)) {
                        $subscription->customer_id = $value->id;
                    }
                    break;
                case "product":
                    if (isset($value->id)) {
                        $subscription->product_id = $value->id;
                    }
                    break;
                case "credit_card":
                    if (isset($value->id)) {
                        $subscription->credit_card_id = $value->id;
                    }
                    break;
                case "bank_account":
                    if (isset($value->id)) {
                        $subscription->bank_account_id = $value->id;
                    }
                    break;
                default:
                    if (property_exists($subscription, $key)) {
                        $subscription->$key = $value;
                    }
            }
        }
        return $subscription;
    }
}