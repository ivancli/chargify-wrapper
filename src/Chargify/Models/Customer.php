<?php

namespace Invigor\Chargify\Models;
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:18 PM
 */
class Customer
{
    public $first_name;
    public $last_name;
    public $email;
    public $cc_emails;
    public $organization;
    public $reference;
    public $id;
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

    public function __construct($id = null)
    {

    }
}