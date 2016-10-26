<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/25/2016
 * Time: 11:43 AM
 */

namespace Invigor\Chargify\Controllers;


use Invigor\Chargify\Models\Invoice;
use Invigor\Chargify\Traits\CacheFlusher;
use Invigor\Chargify\Traits\Curl;

/**
 * Class InvoiceController
 * @package Invigor\Chargify\Controllers
 */
class InvoiceController
{
    use Curl, CacheFlusher;

    /**
     * Load all invoices
     *
     * @param $queryString
     * available queries are as follow:
     *      1. start_date=<YYYY-MM-DD>
     *      2. end_date=<YYYY-MM-DD>
     *      3. status[]=<paid, unpaid, partial, archived>
     *      4. invoice_id=<subscription_id>
     * @return array|mixed
     */
    public function all($queryString = null)
    {
        return $this->__all($queryString);
    }

    /**
     * Load an invoice
     *
     * @param $invoice_id
     * @return Invoice
     */
    public function get($invoice_id)
    {
        return $this->__get($invoice_id);
    }

    /**
     * @param null $queryString
     * @return array|mixed
     */
    private function __all($queryString = null)
    {
        $url = config('chargify.api_domain') . "invoices.json";
        if (!is_null($queryString)) {
            $url .= "?" . $queryString;
        }
        $invoices = $this->_get($url);
        if (is_array($invoices)) {
            $invoices = array_pluck($invoices, 'invoice');
            $output = array();
            foreach ($invoices as $invoice) {
                $output[] = $this->__assign($invoice);
            }
            return $output;
        } else {
            return $invoices;
        }
    }

    /**
     * @param $invoice_id
     * @return Invoice|mixed
     */
    private function __get($invoice_id)
    {
        $url = config('chargify.api_domain') . "invoices/{$invoice_id}.json";
        $invoice = $this->_get($url);
        if (isset($invoice->invoice)) {
            $invoice = $invoice->invoice;
            $invoice = $this->__assign($invoice);
        }
        return $invoice;
    }

    /**
     * @param $input_invoice
     * @return Invoice
     */
    private function __assign($input_invoice)
    {
        $invoice = new Invoice;
        foreach ($input_invoice as $key => $value) {
            if (property_exists($invoice, $key)) {
                $invoice->$key = $value;
            }
        }
        return $invoice;
    }
}