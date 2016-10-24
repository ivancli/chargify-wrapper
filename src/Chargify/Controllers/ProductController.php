<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/24/2016
 * Time: 11:52 AM
 */

namespace Invigor\Chargify\Controllers;


use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Models\Product;
use Invigor\Chargify\Traits\Curl;

class ProductController
{
    use Curl;

    public function get($id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.products.{$id}", config('chargify.caching.ttl'), function () use ($id) {
                return $this->__get($id);
            });
        } else {
            return $this->__get($id);
        }
    }

    private function __get($id)
    {
        $url = config('chargify.api_domain') . "products/{$id}.json";
        $product = $this->_get($url);
        if (!is_null($product)) {
            $product = $product->product;
            $output = $this->__assign($product);
            return $output;
        } else {
            return null;
        }
    }

    private function __assign($input_product)
    {
        $product = new Product;
        foreach ($input_product as $key => $value) {
            switch ($key) {
                case "product_family":
                    if (isset($value->id)) {
                        $product->product_family_id = $value->id;
                    }
                    break;
                default:
                    if (property_exists($product, $key)) {
                        $product->$key = $value;
                    }
            }
        }
        return $product;
    }
}