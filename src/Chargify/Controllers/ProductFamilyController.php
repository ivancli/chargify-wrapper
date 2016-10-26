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


    public function create($fields)
    {
        return $this->__create($fields);
    }

    public function archiveCoupon($product_family_id, $coupon_id)
    {
        return $this->__archiveCoupon($product_family_id, $coupon_id);
    }

    /**
     * load all product families
     *
     * @return array
     */
    public function all()
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families", config('chargify.caching.ttl'), function () {
                return $this->__all();
            });
        } else {
            return $this->__all();
        }
    }

    /**
     * load a product family by product family id
     *
     * @param $product_family_id
     * @return ProductFamily|null
     */
    public function get($product_family_id)
    {
        if (config('chargify.caching.enable') == true) {
            return Cache::remember("chargify.product_families.{$product_family_id}", config('chargify.caching.ttl'), function () use ($product_family_id) {
                return $this->__get($product_family_id);
            });
        } else {
            return $this->__get($product_family_id);
        }
    }

    private function __create($fields)
    {
        $url = config('chargify.api_domain') . "product_families.json";
        $data = array(
            "product_family" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $productFamily = $this->_post($url, $data);
        if (isset($productFamily->product_family)) {
            $productFamily = $this->__assign($productFamily->product_family);
        }
        return $productFamily;
    }

    private function __archiveCoupon($product_family_id, $coupon_id)
    {
        //https://<subdomain>.chargify.com/product_families/<product_family_id>/coupons/<coupon_id>.<format>
        $url = config('chargify.api_domain') . "product_families/{$product_family_id}/coupons/{$coupon_id}.json";
        $coupon = $this->_delete($url);
        if (isset($coupon->coupon)) {
            $coupon = true;
        }
        return $coupon;
    }

    /**
     * @return array
     */
    private function __all()
    {
        $url = config('chargify.api_domain') . "product_families.json";
        $productFamilies = $this->_get($url);
        if (is_array($productFamilies)) {
            $productFamilies = array_pluck($productFamilies, 'product_family');
            $output = array();
            foreach ($productFamilies as $productFamily) {
                $output[] = $this->__assign($productFamily);
            }
            return $output;
        } else {
            return $productFamilies;
        }
    }

    /**
     * @param $product_family_id
     * @return ProductFamily|null
     */
    private function __get($product_family_id)
    {
        $url = config('chargify.api_domain') . "product_families/{$product_family_id}.json";
        $productFamily = $this->_get($url);
        if (!is_null($productFamily)) {
            $productFamily = $productFamily->product_family;
            $output = $this->__assign($productFamily);
            return $output;
        } else {
            return $productFamily;
        }
    }

    /**
     * @param $input_product_family
     * @return ProductFamily
     */
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