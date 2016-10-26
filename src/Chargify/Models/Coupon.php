<?php

namespace Invigor\Chargify\Models;
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:18 PM
 */
class Coupon
{
    public $name;
    public $code;
    public $description;
    public $percentage;
    public $amount;
    public $allow_negative_balance;
    public $recurring;
    public $duration_period_count;
    public $duration_interval_unit;
    public $end_date;
    public $conversion_limit;
    public $product_family_id;
    public $created_at;
    public $start_date;
    public $updated_at;
    public $archived_at;

    public function __construct($id = null)
    {

    }
}