<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/26/2016
 * Time: 5:25 PM
 */

namespace Invigor\Chargify\Controllers;


use Invigor\Chargify\Models\Allocation;
use Invigor\Chargify\Traits\CacheFlusher;
use Invigor\Chargify\Traits\Curl;

class AllocationController
{
    use Curl, CacheFlusher;

    /**
     * Create an allocation
     *
     * @param $subscription_id
     * @param $component_id
     * @param $fields
     * @return Allocation|mixed
     */
    public function create($subscription_id, $component_id, $fields)
    {
        return $this->__create($subscription_id, $component_id, $fields);
    }

    /**
     * Create multiple allocations
     *
     * @param $subscription_id
     * @param $fields
     * @return array|mixed
     */
    public function createMultiple($subscription_id, $fields)
    {
        return $this->__createMultiple($subscription_id, $fields);
    }

    /**
     * Load all allocations from a component
     *
     * @param $subscription_id
     * @param $component_id
     * @param null $page
     * @return array|mixed
     */
    public function allByComponent($subscription_id, $component_id, $page = null)
    {
        return $this->__allByComponent($subscription_id, $component_id, $page);
    }

    /**
     * @param $subscription_id
     * @param $component_id
     * @param $fields
     * @return Allocation|mixed
     */
    private function __create($subscription_id, $component_id, $fields)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/components/{$component_id}/allocations.json";
        $data = array(
            "allocation" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $allocation = $this->_post($url, $data);
        if (isset($allocation->allocation)) {
            $output = $this->__assign($allocation->allocation);
            return $output;
        } else {
            return $allocation;
        }
    }

    /**
     * @param $subscription_id
     * @param $fields
     * @return array|mixed
     */
    private function __createMultiple($subscription_id, $fields)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/allocations.json";
        $data = $fields;
        $data = json_decode(json_encode($data), false);
        $allocations = $this->_post($url, $data);
        if (is_array($allocations)) {
            $allocations = array_pluck($allocations, 'allocation');
            $output = array();
            foreach ($allocations as $allocation) {
                $output[] = $this->__assign($allocation);
            }
            return $output;
        } else {
            return $allocations;
        }
    }

    /**
     * @param $subscription_id
     * @param $component_id
     * @param null $page
     * @return array|mixed
     */
    private function __allByComponent($subscription_id, $component_id, $page = null)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/components/{$component_id}/allocations.json";
        $allocations = $this->_get($url);
        if (is_array($allocations)) {
            $allocations = array_pluck($allocations, 'allocation');
            $output = array();
            foreach ($allocations as $allocation) {
                $output[] = $this->__assign($allocation);
            }
            return $output;
        } else {
            return $allocations;
        }
    }

    /**
     * @param $input_allocation
     * @return Allocation
     */
    private function __assign($input_allocation)
    {
        $allocation = new Allocation;
        foreach ($input_allocation as $key => $value) {
            if (property_exists($allocation, $key)) {
                $allocation->$key = $value;
            }
        }
        return $allocation;
    }
}