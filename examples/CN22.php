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
        "unitGrossPaid" => 6.95,
        "intrastat_code" => 91011100,
        "currency" => 'NZD',
        "origin_country" => 'GB'
    ),
    array(
        "salesQty" => 2,
        "name" => 'Silver Earring',
        "itemMass" => 0.1,
        "unitGrossPaid" => 12.95,
        "intrastat_code" => 91011101,
        "currency" => 'NZD',
        "origin_country" => 'GB'
    ),
    array(
        "salesQty" => 2,
        "name" => 'Silver Earrings with Emeralds',
        "itemMass" => 0.1,
        "unitGrossPaid" => 12.95,
        "intrastat_code" => 91011102,
        "currency" => 'NZD',
        "origin_country" => 'GB'
    ),

);

$details = array(
    "signName" => "Dave Smith",
    "goodsType" => \fhindsthejewellers\GBCustomsForm\CustomsForm::MERCHANDISE,


);

$form = new \fhindsthejewellers\GBCustomsForm\CustomsManager($products,$details);

$pdf = $form->GetPDF();

$pdf->Output();

