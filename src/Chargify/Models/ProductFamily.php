<?php

namespace Invigor\Chargify\Models;

use Invigor\Chargify\Controllers\ProductController;

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

    private $productController;

    public function __construct()
    {
        $this->productController = new ProductController;
    }

    public function products()
    {
        return $this->productController->allByProductFamily($this->id);
    }

    public function components()
    {

    }
}