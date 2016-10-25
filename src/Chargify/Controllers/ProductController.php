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

    public function create()
    {

    }

    /**
     * Load all products
     *
     * @return array
     */
    public function all()
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.products", config('chargify.caching.ttl'), function () {
                return $this->__all();
            });
        } else {
            return $this->__all();
        }
    }

    /**
     * Load a product by product id
     *
     * @param $id
     * @return Product|null
     */
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

    /**
     * Load a product by product handle
     *
     * @param $handle
     * @return Product|null
     */
    public function getByHandle($handle)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.products.handle.{$handle}", config('chargify.caching.ttl'), function () use ($handle) {
                return $this->__getByHandle($handle);
            });
        } else {
            return $this->__getByHandle($handle);
        }
    }

    /**
     * Load all products by product family id
     *
     * @param $id
     * @return array
     */
    public function allByProductFamily($id){
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families.{$id}.products", config('chargify.caching.ttl'), function () use ($id) {
                return $this->__allByProductFamily($id);
            });
        } else {
            return $this->__allByProductFamily($id);
        }
    }

    /**
     * @return array
     */
    private function __all()
    {
        $url = config('chargify.api_domain') . "products.json";
        $products = $this->_get($url);
        if (is_array($products)) {
            $products = array_pluck($products, 'product');
            $output = array();
            foreach ($products as $product) {
                $output[] = $this->__assign($product);
            }
            return $output;
        } else {
            return array();
        }
    }

    /**
     * @param $id
     * @return Product|null
     */
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

    /**
     * @param $handle
     * @return Product|null
     */
    private function __getByHandle($handle)
    {
        $url = config('chargify.api_domain') . "products/handle/{$handle}.json";
        $product = $this->_get($url);
        if (!is_null($product)) {
            $product = $product->product;
            $output = $this->__assign($product);
            return $output;
        } else {
            return null;
        }
    }

    /**
     * @param $id
     * @return array
     */
    private function __allByProductFamily($id)
    {
        $url = config('chargify.api_domain') . "product_families/{$id}/products.json";
        $products = $this->_get($url);
        if (is_array($products)) {
            $products = array_pluck($products, 'product');
            $output = array();
            foreach ($products as $product) {
                $output[] = $this->__assign($product);
            }
            return $output;
        } else {
            return array();
        }
    }

    /**
     * @param $input_product
     * @return Product
     */
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