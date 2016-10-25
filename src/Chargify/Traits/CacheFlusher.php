<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/25/2016
 * Time: 4:17 PM
 */

namespace Invigor\Chargify\Traits;


use Illuminate\Support\Facades\Cache;

trait CacheFlusher
{
    /*------------------------------------------------------------------------------------------------------------
     * Components
     * ----------------------------------------------------------------------------------------------------------*/
    public function flushComponentsByProductFamily($product_family_id)
    {
        Cache::forget("chargify.product_families.{$product_family_id}.components");
    }

    public function flushComponentsBySubscription($subscription_id)
    {
        Cache::forget("chargify.subscriptions.{$subscription_id}.components");
    }

    public function flushComponentByProductFamily($product_family_id, $component_id)
    {
        Cache::forget("chargify.product_families.{$product_family_id}.components.{$component_id}");
    }

    public function flushComponentBySubscription($subscription_id, $component_id)
    {
        Cache::forget("chargify.subscriptions.{$subscription_id}.{$component_id}");
    }

    /*-----------------------------------------------------------------------------------------------------------
     * Products
     * ----------------------------------------------------------------------------------------------------------*/

    /**
     * Flush a product cache
     *
     * @param $id
     */
    public function flushProduct($id)
    {
        Cache::forget("chargify.products.{$id}");
    }

    /**
     * Flush products list cache
     */
    public function flushProducts()
    {
        Cache::forget("chargify.products");
    }

    /**
     * Flush a product cached by handle
     *
     * @param $handle
     */
    public function flushProductHandle($handle)
    {
        Cache::forget("chargify.products.handle.{$handle}");
    }

    /**
     * Flush a list of products cached by product family
     *
     * @param $id
     */
    public function flushProductFamilyProducts($id)
    {
        Cache::forget("chargify.product_families.{$id}.products");
    }

    /*------------------------------------------------------------------------------------------------------------
     * Subscriptions
     *-----------------------------------------------------------------------------------------------------------*/
    /**
     * @param $subscription_id
     */
    public function flushSubscription($subscription_id)
    {
        Cache::forget("chargify.subscriptions.{$subscription_id}");
    }

    /**
     * @param $customer_id
     */
    public function flushSubscriptionByCustomer($customer_id)
    {
        Cache::forget("chargify.customers.{$customer_id}.subscriptions");
    }

}