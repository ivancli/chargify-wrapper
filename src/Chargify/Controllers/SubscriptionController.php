<?php
namespace Invigor\Chargify\Controllers;
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
        $subscription = $this->get($url);
        if (!is_null($subscription)) {
            return $subscription->subscription;
        } else {
            return null;
        }
    }

    public function all()
    {
//        if (config('chargify.caching.enable') == true) {
//            return Cache::remember('chargify.subscriptions.all', config('chargify.caching.ttl'), function () {
//                return (new static)->_all();
//            });
//        } else {
        return $this->_all();
//        }
    }

    private function _all()
    {
        $url = config('chargify.api_domain') . "subscriptions.json";
        $subscriptions = $this->get($url);
        if (is_array($subscriptions)) {
            $subscriptions = array_pluck($subscriptions, 'subscription');
            $output = array();
            foreach ($subscriptions as $subscription) {
                $output[] = (new static)->_set($subscription);
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
        foreach ($input_subscription as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        $end = microtime();
        dump($end - $start);
        return $this;
    }


}