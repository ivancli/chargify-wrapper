<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/25/2016
 * Time: 11:42 AM
 */

namespace Invigor\Chargify\Controllers;

use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\Component;
use Invigor\Chargify\Traits\CacheFlusher;
use Invigor\Chargify\Traits\Curl;

class ComponentController
{
    use Curl, CacheFlusher;

    /**
     * Create a component within a product family
     *
     * @param $product_family_id
     * @param $plural_kind - this variable should either be 'on_off_component', 'quantity_based_component' or 'metered_component'
     * @param $fields
     * @return Component|mixed
     */
    public function create($product_family_id, $plural_kind, $fields)
    {
        $validator = $this->__validate($fields);
        if ($validator['status'] != true) {
            return $validator['errors'];
        }
        return $this->__create($product_family_id, $plural_kind, $fields);
    }

    /**
     * Load all component by product family id
     *
     * @param $product_family_id
     * @return array|mixed
     */
    public function allByProductFamily($product_family_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families.{$product_family_id}.components", config('chargify.caching.ttl'), function () use ($product_family_id) {
                return $this->__allByProductFamily($product_family_id);
            });
        } else {
            return $this->__allByProductFamily($product_family_id);
        }
    }

    /**
     * Load all components by subscription id
     *
     * @param $subscription_id
     * @return array|mixed
     */
    public function allBySubscription($subscription_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.subscriptions.{$subscription_id}.components", config('chargify.caching.ttl'), function () use ($subscription_id) {
                return $this->__allBySubscription($subscription_id);
            });
        } else {
            return $this->__allBySubscription($subscription_id);
        }
    }

    /**
     * Load a component by product family id
     *
     * @param $product_family_id
     * @param $component_id
     * @return Component|null
     */
    public function getByProductFamily($product_family_id, $component_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families.{$product_family_id}.components.{$component_id}", config('chargify.caching.ttl'), function () use ($product_family_id, $component_id) {
                return $this->__getByProductFamily($product_family_id, $component_id);
            });
        } else {
            return $this->__getByProductFamily($product_family_id, $component_id);
        }
    }

    /**
     * load a component by subscription id
     *
     * @param $subscription_id
     * @param $component_id
     * @return Component|mixed
     */
    public function getBySubscription($subscription_id, $component_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.subscriptions.{$subscription_id}.components.{$component_id}", config('chargify.caching.ttl'), function () use ($subscription_id, $component_id) {
                return $this->__getBySubscription($subscription_id, $component_id);
            });
        } else {
            return $this->__getBySubscription($subscription_id, $component_id);
        }
    }

    /**
     * @param $product_family_id
     * @param $plural_kind
     * @param $fields
     * @return Component|mixed
     */
    private function __create($product_family_id, $plural_kind, $fields)
    {
        $url_plural_kind = str_plural($plural_kind);
        $url = config('chargify.api_url') . "product_families/{$product_family_id}/{$url_plural_kind}.json";
        $data = array(
            $plural_kind => $fields
        );
        $data = json_decode(json_encode($data), false);
        $component = $this->_post($url, $data);
        if (isset($component->$plural_kind)) {
            $output = $this->__assign($component->$plural_kind);
            $this->flushComponentsByProductFamily($product_family_id);
            return $output;
        } else {
            return $component;
        }
    }

    /**
     * @param $product_family_id
     * @return array|mixed
     */
    private function __allByProductFamily($product_family_id)
    {
        $url = config('chargify.api_domain') . "product_families/{$product_family_id}/components.json";
        $components = $this->_get($url);
        if (is_array($components)) {
            $components = array_pluck($components, 'component');
            $output = array();
            foreach ($components as $component) {
                $output[] = $this->__assign($component);
            }
            return $output;
        } else {
            return $components;
        }
    }

    private function __allBySubscription($subscription_id)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/components.json";
        $components = $this->_get($url);
        if (is_array($components)) {
            $components = array_pluck($components, 'component');
            $output = array();
            foreach ($components as $component) {
                $output[] = $this->__assign($component);
            }
            return $output;
        } else {
            return $components;
        }
    }

    private function __getByProductFamily($product_family_id, $component_id)
    {
        $url = config('chargify.api_domain') . "product_families/{$product_family_id}/components/{$component_id}.json";
        $component = $this->_get($url);
        if (!is_null($component)) {
            $component = $component->component;
            $output = $this->__assign($component);
            return $output;
        } else {
            return null;
        }
    }

    private function __getBySubscription($subscription_id, $component_id)
    {
        $url = config('chargify.api_domain') . "subscriptions/{$subscription_id}/components/{$component_id}.json";
        $component = $this->_get($url);
        if (!is_null($component)) {
            $component = $component->component;
            $output = $this->__assign($component);
            return $output;
        } else {
            return $component;
        }
    }

    /**
     * @param $input_component
     * @return Component
     */
    private function __assign($input_component)
    {
        $component = new Component;
        foreach ($input_component as $key => $value) {
            if (property_exists($component, $key)) {
                $component->$key = $value;
            }
        }
        return $component;
    }

    /**
     * @param $fields
     * @return array
     */
    private function __validate($fields)
    {
        $status = true;
        $errors = [];
        $required_fields = array(
            "name", "unit_name", "pricing_scheme", "prices"
        );
        foreach ($required_fields as $required_field) {
            if (!isset($fields[$required_field])) {
                $status = false;
                $errors[] = "{$required_field} is required.";
            }
        }
        if ($status === false) {
            return compact(['status', 'errors']);
        }
        return compact(['status']);
    }
}