<?php

namespace Invigor\Chargify\Models;

use Invigor\Chargify\Controllers\SubscriptionController;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:18 PM
 */

/**
 * Please check
 * https://docs.chargify.com/api-customers
 * for related documentation provided by Chargify
 *
 * Class Customer
 * @package Invigor\Chargify\Models
 */
class Customer
{
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $cc_emails;
    public $organization;
    public $reference;
    public $created_at;
    public $updated_at;
    public $vat_number;
    public $address;
    public $address_2;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $phone;

    private $subscriptionController;

    public function __construct($id = null)
    {
        $this->subscriptionController = new SubscriptionController;
    }

    public function subscriptions()
    {
        return $this->subscriptionController->allByCustomer($this->id);
    }
}