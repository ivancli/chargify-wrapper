<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/24/2016
 * Time: 11:58 AM
 */

namespace Invigor\Chargify\Controllers;


use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\ProductFamily;
use Invigor\Chargify\Traits\Curl;

class ProductFamilyController
{
    use Curl;

    public function get($id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families.{$id}", config('chargify.caching.ttl'), function () use ($id) {
                return $this->__get($id);
            });
        } else {
            return $this->__get($id);
        }
    }

    private function __get($id)
    {
        $url = config('chargify.api_domain') . "product_families/{$id}.json";
        $productFamily = $this->_get($url);
        if (!is_null($productFamily)) {
            $productFamily = $productFamily->product_family;
            $output = $this->__assign($productFamily);
            return $output;
        } else {
            return null;
        }
    }

    private function __assign($input_product_family)
    {
        $productFamily = new ProductFamily;
        foreach ($input_product_family as $key => $value) {
            if (property_exists($productFamily, $key)) {
                $productFamily->$key = $value;
            }
        }
        return $productFamily;
    }
}