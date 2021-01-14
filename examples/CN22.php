<?php
require_once '../src/include.php';
require_once __DIR__ . '/../vendor/autoload.php';
ini_set ('error_reporting', E_ALL);
ini_set ('display_errors', '1');
error_reporting (E_ALL|E_STRICT);


$products = array(
    array(
        "salesQty" => 1,
        "name" => 'Silver Raindrop Earrings',
        "itemMass" => 0.001,
        "intrastat_code" => 91011100,
        "origin_country" => 'GB',
        "value" => array (
            "value" => 6.95,
            "currency" => "NZD",
            "exchangeRate" => "1.3"
        )
    ),
    array(
        "salesQty" => 2,
        "name" => 'Silver Earring',
        "itemMass" => 0.1,
        "intrastat_code" => 91011101,
        "origin_country" => 'GB',
        "value" => array (
            "value" => 6.95,
            "currency" => "NZD",
            "exchangeRate" => "1.3"
        )
    ),
    array(
        "salesQty" => 2,
        "name" => 'Silver Earrings with Emeralds',
        "itemMass" => 0.1,
        "intrastat_code" => 91011102,
        "origin_country" => 'GB',
        "value" => array (
            "value" => 6.95,
            "currency" => "NZD",
            "exchangeRate" => "1.3"
        )
    ),

);

$details = array(
    "signName" => "Dave Smith",
    "goodsType" => \fhindsthejewellers\GBCustomsForm\CustomsForm::MERCHANDISE,
    "vatNumber" => "GB000000000",
    "fromAddress" => array(
        "name" => "Dave Smith",
        "business" => "F Hinds Ltd",
        "street" => "24 Park Road",
        "city" => "Uxbridge",
        "zip" => "UB8 1NH",
        "country" => "GB"
    ),


);

$form = new \fhindsthejewellers\GBCustomsForm\CustomsManager($products,$details);

$pdf = $form->GetPDF();

$pdf->Output();

