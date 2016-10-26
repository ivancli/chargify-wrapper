<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/25/2016
 * Time: 11:42 AM
 */

namespace Invigor\Chargify\Controllers;


use Invigor\Chargify\Models\Coupon;
use Invigor\Chargify\Traits\CacheFlusher;
use Invigor\Chargify\Traits\Curl;

class CouponController
{
    use Curl, CacheFlusher;

    /**
     * Create a new coupon
     *
     * @param $fields
     * @return Coupon|mixed
     */
    public function create($fields)
    {
        return $this->__create($fields);
    }

    /**
     * Update a coupon
     *
     * @param $coupon_id
     * @param $fields
     * @return Coupon|mixed
     */
    public function update($coupon_id, $fields)
    {
        return $this->__update($coupon_id, $fields);
    }

    /**
     * Archive an existing coupon
     *
     * @param $coupon_id
     * @return bool|mixed
     */
    public function archive($coupon_id)
    {
        return $this->__archive($coupon_id);
    }

    /**
     * Load a coupon
     *
     * @param $coupon_id
     * @return Coupon|mixed
     */
    public function get($coupon_id)
    {
        return $this->__get($coupon_id);
    }

    /**
     * Find a coupon by coupon code
     *
     * @param $coupon_code
     * @param null $product_family_id
     * @return Coupon|mixed
     */
    public function find($coupon_code, $product_family_id = null)
    {
        return $this->__find($coupon_code, $product_family_id);
    }

    /**
     * Load usage of a coupon
     *
     * @param $coupon_id
     * @return mixed
     */
    public function getUsage($coupon_id)
    {
        return $this->__getUsage($coupon_id);
    }

    /**
     * Check a coupon code's validity
     *
     * @param $coupon_code
     * @return Coupon|mixed
     */
    public function validate($coupon_code)
    {
        return $this->__validate($coupon_code);
    }

    /**
     * Load all coupon subcode of a coupon
     *
     * @param $coupon_id
     * @param null $page
     * @param null $per_page
     * @return mixed
     */
    public function allSubcodes($coupon_id, $page = null, $per_page = null)
    {
        return $this->__allSubcodes($coupon_id, $page, $per_page);
    }

    /**
     * Create a new coupon subcode under a coupon
     *
     * @param $coupon_id
     * @param $fields
     * @return mixed
     */
    public function createSubcodes($coupon_id, $fields)
    {
        return $this->__createSubcodes($coupon_id, $fields);
    }

    /**
     * Update a list of coupon subcodes under a coupon
     *
     * @param $coupon_id
     * @param $fields
     * @return Coupon|mixed
     */
    public function updateSubcodes($coupon_id, $fields)
    {
        return $this->__updateSubcodes($coupon_id, $fields);
    }

    /**
     * Delete a specific coupon subcode of a coupon
     *
     * @param $coupon_id
     * @param $coupon_subcode
     * @return bool|mixed
     */
    public function deleteSubcode($coupon_id, $coupon_subcode)
    {
        return $this->__deleteSubcode($coupon_id, $coupon_subcode);
    }

    /**
     * @param $fields
     * @return Coupon|mixed
     */
    private function __create($fields)
    {
        $url = config('chargify.api_domain') . "coupons.json";
        $data = array(
            "coupon" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $coupon = $this->_post($url, $data);
        if (isset($coupon->coupon)) {
            $coupon = $this->__assign($coupon->coupon);
        }
        return $coupon;
    }

    /**
     * @param $coupon_id
     * @param $fields
     * @return Coupon|mixed
     */
    private function __update($coupon_id, $fields)
    {
        $url = config('chargify.api_domain') . "coupons/{$coupon_id}.json";
        $data = array(
            "coupon" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $coupon = $this->_put($url, $data);
        if (isset($coupon->coupon)) {
            $coupon = $this->__assign($coupon->coupon);
        }
        return $coupon;
    }

    /**
     * @param $coupon_id
     * @return bool|mixed
     */
    private function __archive($coupon_id)
    {
        $url = config('chargify.api_domain') . "coupons/{$coupon_id}.json";
        $coupon = $this->_delete($url);
        if (is_null($coupon)) {
            $coupon = true;
        }
        return $coupon;
    }

    /**
     * @param $coupon_id
     * @return Coupon|mixed
     */
    private function __get($coupon_id)
    {
        $url = config('chargify.api_domain') . "coupons/{$coupon_id}.json";
        $coupon = $this->_get($url);
        if (isset($coupon->coupon)) {
            $coupon = $coupon->coupon;
            $coupon = $this->__assign($coupon);
        }
        return $coupon;
    }

    /**
     * @param $coupon_code
     * @param null $product_family_id
     * @return Coupon|mixed
     */
    private function __find($coupon_code, $product_family_id = null)
    {
        $url = config('chargify.api_domain') . "coupons/find.json?code={$coupon_code}";
        if (!is_null($product_family_id)) {
            $url .= "&product_family_id={$product_family_id}";
        }
        $coupon = $this->_get($url);
        if (isset($coupon->coupon)) {
            $coupon = $coupon->coupon;
            $coupon = $this->__assign($coupon);
        }
        return $coupon;
    }

    /**
     * @param $coupon_id
     * @return mixed
     */
    private function __getUsage($coupon_id)
    {
        $url = config('chargify.api_domain') . "coupons/{$coupon_id}/usage.json";
        $usage = $this->_get($url);
        return $usage;
    }

    /**
     * @param $coupon_code
     * @return Coupon|mixed
     */
    private function __validate($coupon_code)
    {
        $url = config('chargify.api_domain') . "coupons/validate.json?code={$coupon_code}";
        $coupon = $this->_get($url);
        if (isset($coupon->coupon)) {
            $coupon = $coupon->coupon;
            $coupon = $this->__assign($coupon);
        }
        return $coupon;
    }

    /**
     * @param $coupon_id
     * @param $page
     * @param $per_page
     * @return mixed
     */
    private function __allSubcodes($coupon_id, $page, $per_page)
    {
        $url = config('chargify.api_domain') . "coupons/{$coupon_id}/codes.json";
        if (!is_null($page) && !is_null($per_page)) {
            $url .= "?page={$page}&per_page={$per_page}";
        }
        $couponSubcodes = $this->_get($url);
        return $couponSubcodes;
    }

    /**
     * @param $coupon_id
     * @param $fields
     * @return mixed
     */
    private function __createSubcodes($coupon_id, $fields)
    {
        $url = config('chargify.api_domain') . "coupons/$coupon_id/codes.json";
        $data = array(
            "codes" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $coupon = $this->_post($url, $data);
        return $coupon;
    }

    /**
     * @param $coupon_id
     * @param $fields
     * @return Coupon|mixed
     */
    private function __updateSubcodes($coupon_id, $fields)
    {
        $url = config('chargify.api_domain') . "coupons/{$coupon_id}/codes.json";
        $data = array(
            "codes" => $fields
        );
        $data = json_decode(json_encode($data), false);
        $coupon = $this->_put($url, $data);
        if (isset($coupon->coupon)) {
            $coupon = $this->__assign($coupon->coupon);
        }
        return $coupon;
    }

    /**
     * @param $coupon_id
     * @param $coupon_subcode
     * @return bool|mixed
     */
    private function __deleteSubcode($coupon_id, $coupon_subcode)
    {
        $url = config('chargify.api_domain') . "coupons/{$coupon_id}/{$coupon_subcode}.json";
        $coupon = $this->_delete($url);
        if (is_null($coupon)) {
            $coupon = true;
        }
        return $coupon;
    }


    /**
     * @param $input_coupon
     * @return Coupon
     */
    private function __assign($input_coupon)
    {
        $coupon = new Coupon;
        foreach ($input_coupon as $key => $value) {
            if (property_exists($coupon, $key)) {
                $coupon->$key = $value;
            }
        }
        return $coupon;
    }

}