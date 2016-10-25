<?php
namespace Invigor\Chargify;

use Invigor\Chargify\Controllers\ComponentController;
use Invigor\Chargify\Controllers\CouponController;
use Invigor\Chargify\Controllers\CustomerController;
use Invigor\Chargify\Controllers\InvoiceController;
use Invigor\Chargify\Controllers\ProductController;
use Invigor\Chargify\Controllers\ProductFamilyController;
use Invigor\Chargify\Controllers\SiteController;
use Invigor\Chargify\Controllers\SubscriptionController;
use Invigor\Chargify\Controllers\TransactionController;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:07 AM
 */
class Chargify
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public static function component()
    {
        return new ComponentController();
    }

    public static function coupon()
    {
        return new CouponController();
    }

    public static function customer()
    {
        return new CustomerController();
    }

    public static function invoice()
    {
        return new InvoiceController();
    }

    public static function product()
    {
        return new ProductController();
    }

    public static function productFamily()
    {
        return new ProductFamilyController();
    }

    public static function site()
    {
        return new SiteController();
    }

    public static function subscription()
    {
        return new SubscriptionController();
    }

    public static function transaction()
    {
        return new TransactionController();
    }
}