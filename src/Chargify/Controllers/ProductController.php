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
use Invigor\Chargify\Traits\CacheFlusher;
use Invigor\Chargify\Traits\Curl;

class ProductController
{
    use Curl, CacheFlusher;

    /**
     * Create a product
     *
     * @param $product_family_id
     * @param $fields
     * @return Product|mixed
     */
    public function create($product_family_id, $fields)
    {
        $validator = $this->__validate($fields);
        if ($validator['status'] != true) {
            return $validator['errors'];
        }
        return $this->__create($product_family_id, $fields);
    }

    /**
     * Update a product
     *
     * @param $product_id
     * @param $fields
     * @return Product|mixed
     */
    public function update($product_id, $fields)
    {
        return $this->__update($product_id, $fields);
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
     * @param $product_id
     * @return Product|null
     */
    public function get($product_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.products.{$product_id}", config('chargify.caching.ttl'), function () use ($product_id) {
                return $this->__get($product_id);
            });
        } else {
            return $this->__get($product_id);
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
     * @param $product_family_id
     * @return array
     */
    public function allByProductFamily($product_family_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families.{$product_family_id}.products", config('chargify.caching.ttl'), function () use ($product_family_id) {
                return $this->__allByProductFamily($product_family_id);
            });
        } else {
            return $this->__allByProductFamily($product_family_id);
        }
    }

    /**
     * @param $product_family_id
     * @param $fields
     * @return Product|mixed
     */
    private function __create($product_family_id, $fields)
    {
        $url = config('chargify.api_url') . "product_families/{$product_family_id}/products.json";
        $data = array(
            "product" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $product = $this->_post($url, $data);
        if (isset($product->product)) {
            $output = $this->__assign($product->product);
            $this->flushProducts();
            $this->flushProductFamilyProducts($output->product_family_id);
            return $output;
        } else {
            return $product;
        }
    }

    /**
     * @param $product_id
     * @param $fields
     * @return Product|mixed
     */
    private function __update($product_id, $fields)
    {
        $url = config('chargify.api_url') . "products/{$product_id}.json";
        $data = array(
            "product" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $product = $this->_put($url, $data);
        if (isset($product->product)) {
            $output = $this->__assign($product->product);
            $this->flushProducts();
            $this->flushProductFamilyProducts($output->product_family_id);
            return $output;
        } else {
            return $product;
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
            return $products;
        }
    }

    /**
     * @param $product_id
     * @return Product|null
     */
    private function __get($product_id)
    {
        $url = config('chargify.api_domain') . "products/{$product_id}.json";
        $product = $this->_get($url);
        if (!is_null($product)) {
            $product = $product->product;
            $output = $this->__assign($product);
            return $output;
        } else {
            return $product;
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
     * @param $product_family_id
     * @return array
     */
    private function __allByProductFamily($product_family_id)
    {
        $url = config('chargify.api_domain') . "product_families/{$product_family_id}/products.json";
        $products = $this->_get($url);
        if (is_array($products)) {
            $products = array_pluck($products, 'product');
            $output = array();
            foreach ($products as $product) {
                $output[] = $this->__assign($product);
            }
            return $output;
        } else {
            return $products;
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

    /**
     * @param $fields
     * @return array
     */
    private function __validate($fields)
    {
        $status = true;
        $errors = [];
        $required_fields = array(
            "price_in_cents", "name", "handle", "description", "request_credit_card", "auto_create_signup_page"
        );
        if (!isset($fields['interval_unit']) || ($fields['interval_unit'] != "month" && $fields['interval_unit'] != "day")) {
            $status = false;
            $errors[] = "interval_unit needs to be either 'month' or 'day'.";
        }
        if (!isset($fields['interval']) || !is_int($fields['interval'])) {
            $status = false;
            $errors[] = "interval needs to be an integer.";
        }
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