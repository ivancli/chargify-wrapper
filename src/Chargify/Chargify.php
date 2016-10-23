<?php
namespace Invigor\Chargify;

use Invigor\Chargify\Controllers\SubscriptionController;
use Invigor\Chargify\Models\Component;
use Invigor\Chargify\Models\Coupon;
use Invigor\Chargify\Models\CouponSubCode;
use Invigor\Chargify\Models\Customer;
use Invigor\Chargify\Models\Invoice;
use Invigor\Chargify\Models\Product;
use Invigor\Chargify\Models\ProductFamily;
use Invigor\Chargify\Models\Site;
use Invigor\Chargify\Models\Subscription;
use Invigor\Chargify\Models\Transaction;

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

    public function component($id = null)
    {
        return new Component($id);
    }

    public function coupon($id = null)
    {
        return new Coupon($id);
    }

    public function couponSubCode($id = null)
    {
        return new CouponSubCode($id);
    }

    public function customer($id = null)
    {
        return new Customer($id);
    }

    public function invoice($id = null)
    {
        return new Invoice($id);
    }

    public function product($id = null)
    {
        return new Product($id);
    }

    public function productFamily($id = null)
    {
        return new ProductFamily($id);
    }

    public function site($id = null)
    {
        return new Site($id);
    }

    public static function subscription()
    {
        return new SubscriptionController();
    }

    public function transaction($id = null)
    {
        return new Transaction($id);
    }
}