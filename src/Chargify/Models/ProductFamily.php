<?php

namespace Invigor\Chargify\Models;
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:12 PM
 */
class ProductFamily
{
    public $id;
    public $name;
    public $description;
    public $handle;
    public $accounting_code;

    public function __construct($id = null)
    {

    }
}