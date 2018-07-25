<?php namespace fhindsthejewellers\GBCustomsForm;


abstract class CustomsForm {
    protected $pdf;
    protected $name;
    protected $parcelMass=0; //In kg
    protected $date;
    protected $goodsType;
    protected $totalProductMass=0; //In kg
    protected $totalValue=0; //In kg
    protected $totalValueCurrency; //In kg
    
    const OTHER = 0;
    const GIFT = 1;
    const DOCUMENTS = 2;
    const COMMERCIALSAMPLE = 3;
    const RETURNEDGOODS = 4;
    const MERCHANDISE = 5;
    
    
    public function __construct(array $nonProductDetails) {
        if (isset($nonProductDetails["signName"])) {
            $this->name = $nonProductDetails["signName"];
        } else {
            throw new \Exception("No signName provided to sign the customs form");
        }
        
        if (isset($nonProductDetails["goodsType"]) and gettype($nonProductDetails["goodsType"])=="integer" ) {
            $this->goodsType = $nonProductDetails["goodsType"];
        } else {
            throw new \Exception("No/Invalid goodsType provided");
        }
        
        if (isset($nonProductDetails["parcelMass"])) {
            if(gettype($nonProductDetails["parcelMass"])=="double" or gettype($nonProductDetails["parcelMass"])=="integer") {
                $this->parcelMass = $nonProductDetails["parcelMass"];
            } else {
                throw new \Exception("parcelMass is not of type double");
            }
        }
        
        if (isset($nonProductDetails["date"])) {
            $this->date=$nonProductDetails["date"];
        } else {
            $this->date=date("Y-m-d");
        }
    }
    
    public function BuildForm(array $products) {
        $this->WriteProducts($products);
        $this->TickGoodsBox();
        $this->SignAndDate();
        $this->WriteTotalWeight();
        $this->WriteTotalValue();
    }
    
    public function GetPDF() : \FPDF {
        return $this->pdf;
    }
    
    abstract protected function TickGoodsBox();
    
    abstract protected function SignAndDate();
    
    abstract protected function WriteTotalWeight();
    
    abstract protected function WriteTotalValue();
    
    abstract protected function WriteProducts(array $products);
}
