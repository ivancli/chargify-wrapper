<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/26/2016
 * Time: 5:25 PM
 */

namespace Invigor\Chargify\Models;


use Invigor\Chargify\Controllers\ComponentController;
use Invigor\Chargify\Controllers\SubscriptionController;

class Allocation
{
    public $component_id;
    public $subscription_id;
    public $quantity;
    public $previous_quantity;
    public $memo;
    public $timestamp;
    public $proration_upgrade_scheme;
    public $proration_downgrade_scheme;
    public $payment;

    private $subscriptionController;

    public function __construct()
    {
        $this->subscriptionController = new SubscriptionController;
    }

    public function subscription()
    {
        return $this->subscriptionController->get($this->subscription_id);
    }

}