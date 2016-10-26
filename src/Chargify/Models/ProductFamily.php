<?php

namespace Invigor\Chargify\Models;

use Invigor\Chargify\Controllers\ComponentController;
use Invigor\Chargify\Controllers\ProductController;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:12 PM
 */

/**
 * Please check
 * https://docs.chargify.com/api-product-families
 * for related documentation provided by Chargify
 *
 * Class ProductFamily
 * @package Invigor\Chargify\Models
 */
class ProductFamily
{
    public $id;
    public $name;
    public $description;
    public $handle;
    public $accounting_code;

    private $productController;
    private $componentController;

    public function __construct()
    {
        $this->productController = new ProductController;
        $this->componentController = new ComponentController;
    }

    public function products()
    {
        return $this->productController->allByProductFamily($this->id);
    }

    public function components()
    {
        return $this->componentController->allByProductFamily($this->id);
    }
}