<?php
namespace Invigor\Chargify\Controllers;

use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\Customer;
use Invigor\Chargify\Models\Subscription;
use Invigor\Chargify\Traits\CacheFlusher;
use Invigor\Chargify\Traits\Curl;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/24/2016
 * Time: 9:33 AM
 */
class SubscriptionController
{
    use Curl, CacheFlusher;

    /**
     * Create a new subscription
     *
     * @param array $fields
     * @return Subscription|null
     */
    public function create(array $fields = array())
    {
        $validator = $this->__validate($fields);
        if ($validator['status'] != true) {
            return $validator['errors'];
        }
        return $this->__create($fields);
    }

    /**
     * Create a preview subscription
     *
     * @param array $fields
     * @return mixed|null
     */
    public function preview(array $fields = array())
    {
        $validator = $this->__validate($fields);
        if ($validator['status'] != true) {
            return $validator['errors'];
        }
        return $this->__preview($fields);
    }

    /**
     * Create a preview renewal subscription
     *
     * @param $subscription_id
     * @return null
     */
    public function previewRenew($subscription_id)
    {
        return $this->__previewRenew($subscription_id);
    }

    /**
     * Load subscriptions in pagination
     *
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function all($offset = 1, $length = 200)
    {
        //this function cannot be managed by Cache easily without TAG, since API forced the output to be paginated.
        return $this->__all($offset, $length);
    }

    /**
     * Load a subscription by subscription id
     *
     * @param $subscription_id
     * @return Subscription|null
     */
    public function get($subscription_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.subscriptions.{$subscription_id}", config('chargify.caching.ttl'), function () use ($subscription_id) {
                return $this->__get($subscription_id);
            });
        } else {
            return $this->__get($subscription_id);
        }
    }

    /**
     * load all subscriptions by customer id
     *
     * @param $customer_id
     * @return array
     */
    public function allByCustomer($customer_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.customers.{$customer_id}.subscriptions", config('chargify.caching.ttl'), function () use ($customer_id) {
                return $this->__allByCustomer($customer_id);
            });
        } else {
            return $this->__allByCustomer($customer_id);
        }
    }

    /**
     * @param $fields
     * @return Subscription|null
     */
    private function __create($fields)
    {
        $url = config('chargify.api_url') . "subscriptions.json";
        $data = array(
            "subscription" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $subscription = $this->_post($url, $data);
        if (isset($subscription->subscription)) {
            $output = $this->__assign($subscription->subscription);
            $this->flushSubscriptionByCustomer($output->customer_id);
            return $output;
        } else {
            return $subscription;
        }
    }

    /**
     * @param $fields
     * @return mixed|null
     */
    private function __preview($fields)
    {
        $url = config('chargify.api_url') . "subscriptions/preview.json";
        $data = array(
            "subscription" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $subscriptionPreview = $this->_post($url, $data);
        if (isset($subscriptionPreview->subscription_preview)) {
            $subscriptionPreview = $subscriptionPreview->subscription_preview;
            return $subscriptionPreview;
        } else {
            return $subscriptionPreview;
        }
    }

    /**
     * @param $id
     * @return mixed|null
     */
    private function __previewRenew($id)
    {
        $url = config('chargify.api_url') . "subscriptions/{$id}/renewals/preview.json";
        $renewalPreview = $this->_post($url);
        if (isset($renewalPreview->renewal_preview)) {
            $renewalPreview = $renewalPreview->renewal_preview;
            return $renewalPreview;
        } else {
            return $renewalPreview;
        }
    }


    /**
     * @param $offset
     * @param $length
     * @return array
     */
    private function __all($offset, $length)
    {
        $url = config('chargify.api_domain') . "subscriptions.json";
        if ($offset >= 0 && $length > 0) {
            $page = ceil($offset / $length);
            $url .= "?per_page={$length}&page={$page}";
        }
        $subscriptions = $this->_get($url);
        if (is_array($subscriptions)) {
            $subscriptions = array_pluck($subscriptions, 'subscription');
            $output = array();
            foreach ($subscriptions as $subscription) {
                $output[] = $this->__assign($subscription);
            }
            return $output;
        } else {
            return $subscriptions;
        }
    }

    /**
     * @param $subscription_id
     * @return Subscription|null
     */
    private function __get($subscription_id)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}.json";
        $subscription = $this->_get($url);
        if (!is_null($subscription)) {
            $subscription = $subscription->subscription;
            $output = $this->__assign($subscription);
            return $output;
        } else {
            return $subscription;
        }
    }

    /**
     * @param $customer_id
     * @return array
     */
    private function __allByCustomer($customer_id)
    {
        $url = config('chargify.api_domain') . "customers/{$customer_id}/subscriptions.json";
        $subscriptions = $this->_get($url);
        if (is_array($subscriptions)) {
            $subscriptions = array_pluck($subscriptions, 'subscription');
            $output = array();
            foreach ($subscriptions as $subscription) {
                $output[] = $this->__assign($subscription);
            }
            return $output;
        } else {
            return $subscriptions;
        }
    }

    /**
     * @param $input_subscription
     * @return Subscription
     */
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

    /**
     * @param $fields
     * @return array
     */
    private function __validate($fields)
    {
        $status = true;
        $errors = [];
        if (!isset($fields['product_handle']) && !isset($fields['product_id'])) {
            $status = false;
            $errors[] = "product_handle or product_id is required";
        }
        if (!isset($fields['customer_attributes']) && !isset($fields['customer_id']) && !isset($fields['customer_reference'])) {
            $status = false;
            $errors[] = "please provide customer_attributes or customer_id or customer reference.";
        }
        if ($status === false) {
            return compact(['status', 'errors']);
        }
        return compact(['status']);
    }
}