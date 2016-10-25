<?php
namespace Invigor\Chargify\Models;
use Invigor\Chargify\Controllers\ProductFamilyController;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/10/2016
 * Time: 1:13 PM
 */
class Component
{
    public $id;
    public $description;
    public $name;
    public $unit_name_;
    public $unit_price;
    public $pricing_scheme;
    public $prices;
    public $product_family_id;
    public $kind;
    public $price_per_unit_in_cents;
    public $archived;
    public $taxable;

    private $productFamilyController;

    public function __construct()
    {
        $this->productFamilyController = new ProductFamilyController();
    }

    public function productFamily()
    {
        return $this->productFamilyController->get($this->product_family_id);
    }

    /*TODO create save and delete method*/
}