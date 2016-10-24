<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/24/2016
 * Time: 10:31 AM
 */

namespace Invigor\Chargify\Controllers;


use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\Customer;
use Invigor\Chargify\Traits\Curl;

class CustomerController
{
    use Curl;

    public function get($id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.customers.{$id}", config('chargify.caching.ttl'), function () use ($id) {
                return $this->__get($id);
            });
        } else {
            return $this->__get($id);
        }
    }

    private function __get($id)
    {
        $url = config('chargify.api_domain') . "customers/{$id}.json";
        $customer = $this->_get($url);
        if (!is_null($customer)) {
            $customer = $customer->customer;
            $output = $this->__assign($customer);
            return $output;
        } else {
            return null;
        }
    }

    private function __assign($input_customer)
    {
        $customer = new Customer;
        foreach ($input_customer as $key => $value) {
            if (property_exists($customer, $key)) {
                $customer->$key = $value;
            }
        }
        return $customer;
    }
}