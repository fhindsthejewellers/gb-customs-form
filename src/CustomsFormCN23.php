<?php namespace fhindsthejewellers\GBCustomsForm;

class CustomsFormCN23 extends CustomsForm {
    protected $shippingCost;
    protected $shippingCurrency;
    protected $invoice;
    protected $importerDetails;
    protected $fromAddress;
    protected $toAddress;
    
    private $formPosition = array (100.5,107.5,115,123);
    
    protected $overrun;
    
    const DESCWIDTH = 72;
    
    public function __construct(array $nonProductDetails) {
        $this->pdf = new \FPDF('L','mm','A4');
        $this->pdf->AddPage();
        $this->pdf->image(__DIR__."/cn23.png",0,0,295.08);
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetMargins(1,0);
        $this->pdf->SetAutoPageBreak(true,5);
        
        if (isset($nonProductDetails["shippingCost"])) {
            if(gettype($nonProductDetails["shippingCost"])=="double" or gettype($nonProductDetails["shippingCost"])=="integer") {
                $this->shippingCost= $nonProductDetails["shippingCost"];
            } else {
                throw new \Exception("shippingCost is not of type double");
            }
        } else {
            throw new \Exception("shippingCost is missing");
        }
        
        if (isset($nonProductDetails["shippingCurrency"])) {
            $this->shippingCurrency= $nonProductDetails["shippingCurrency"];
        } else {
            throw new \Exception("shippingCurrency is missing");
        }
        
        if (isset($nonProductDetails["invoice"])) {
            $this->invoice= $nonProductDetails["invoice"];
        }
        
        if (isset($nonProductDetails["toAddress"])) {
            $this->toAddress= new Address($nonProductDetails["toAddress"]);
        } else {
            throw new \Exception("toAddress is missing");
        }
        
        if (isset($nonProductDetails["fromAddress"])) {
            $this->fromAddress= new Address($nonProductDetails["fromAddress"]);
        } else {
            throw new \Exception("fromAddress is missing");
        }
        
        if (isset($nonProductDetails["importerContact"])) {
            $this->importerContact=$nonProductDetails["importerContact"];
        }
        
        parent::__construct($nonProductDetails);
    }
    
    public function BuildForm(array $products) {
        parent::BuildForm($products);
        $this->WritePostalCharge();
        $this->WriteInvoice();
        $this->WriteAddresses();
        $this->WriteImporterContact();
        if (isset($this->overrun)) {
            $this->WriteOverrun();
        }
    }
    
    protected function TickGoodsBox() {
        switch ($this->goodsType) {
            case self::GIFT:
                $this->pdf->SetXY(27,147);
                break;
            case self::DOCUMENTS:
                $this->pdf->SetXY(27,153);
                break;
            case self::COMMERCIALSAMPLE:
                $this->pdf->SetXY(80,141);
                break;
            case self::RETURNEDGOODS:
                $this->pdf->SetXY(80,147);
                break;
            case self::MERCHANDISE:
                $this->pdf->SetXY(150,147);
                $this->pdf->SetFont('Courier','',10);
                $this->pdf->Cell(20,0,'Merchandise',0,0);
            default:
                $this->pdf->SetXY(80,153);
                break;
        }
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->Cell(2,0,'X',0,0);
    }
    
    protected function WritePostalCharge() {
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetXY(240,135);
        $this->pdf->Cell(57,0,round($this->shippingCost,2)." ".$this->shippingCurrency,0,0);
    }
    
    protected function WriteImporterContact() {
        if (isset($this->importerContact)) {
            $this->pdf->SetFont('Courier','',10);
            $this->pdf->SetXY(157,83);
            $this->pdf->Cell(57,0,$this->importerContact,0,0);
        }
    }
    
    protected function WriteInvoice() {
        if (isset($this->invoice)) {
            $this->pdf->SetFont('Courier','',10);
            $this->pdf->SetXY(132,188);
            $this->pdf->Cell(2,0,'X',0,0);
            $this->pdf->SetXY(140,197);
            $this->pdf->Cell(57,0,$this->invoice,0,0);
        }
    }
    
    protected function WriteAddresses() {
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetXY(45,16);
        if (isset($this->fromAddress->name)) {$this->pdf->Cell(2,0,$this->fromAddress->name,0,0);}
        $this->pdf->SetXY(45,23);
        if (isset($this->fromAddress->business)) {$this->pdf->Cell(2,0,$this->fromAddress->business,0,0);}
        $this->pdf->SetXY(45,31);
        $this->pdf->Cell(2,0,$this->fromAddress->street,0,0);
        $this->pdf->SetXY(45,39);
        $this->pdf->Cell(2,0,$this->fromAddress->zip,0,0);
        $this->pdf->SetXY(95,39);
        $this->pdf->Cell(2,0,$this->fromAddress->city,0,0);
        $this->pdf->SetXY(45,46);
        $this->pdf->Cell(2,0,$this->fromAddress->country,0,0);
        
        $this->pdf->SetXY(45,54);
        if (isset($this->toAddress->name)) {$this->pdf->Cell(2,0,$this->toAddress->name,0,0);}
        $this->pdf->SetXY(45,61);
        if (isset($this->toAddress->business)) {$this->pdf->Cell(2,0,$this->toAddress->business,0,0);}
        $this->pdf->SetXY(45,68.5);
        $this->pdf->Cell(2,0,$this->toAddress->street,0,0);
        $this->pdf->SetXY(45,76);
        $this->pdf->Cell(2,0,$this->toAddress->zip,0,0);
        $this->pdf->SetXY(95,76);
        $this->pdf->Cell(2,0,$this->toAddress->city,0,0);
        $this->pdf->SetXY(45,83);
        $this->pdf->Cell(2,0,$this->toAddress->country,0,0);
    }
    
    protected function SignAndDate() {
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetXY(240,150);
        $this->pdf->Cell(57,0,$this->date,0,0);
        $this->pdf->SetXY(195,197);
        $this->pdf->Cell(0,0,$this->date.' '.$this->name,0,0);
    }
    
    protected function WriteTotalWeight() {
        if ($this->parcelMass > $this->totalProductMass) {
            $mass = $this->parcelMass;
        } else {
            $mass=$this->totalProductMass;
        }
        $this->pdf->SetXY(147,136);
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->Cell(19,0,$mass,0,0);
    }
    
    protected function WriteTotalValue() {
        $this->pdf->SetXY(178,131);
        $this->pdf->SetFont('Courier','',8);
        $this->pdf->Cell(15,5,round($this->totalValue,2),0,2);
        $this->pdf->Cell(15,0,$this->totalValueCurrency,0,0);
    }
    
    protected function WriteProducts(array $products) {
        $originHs1 = "";
        $originHs2 = "";
        $hsCode= "";
        $origin = "";
        $currency = "GBP";
        foreach ($products as $key=>$product) {
            //echo $this->pdf->GetStringWidth($description) . PHP_EOL;
            $mass = $product["itemMass"]*$product["salesQty"];
            $value = $product["value"]["value"]*$product["salesQty"];
            $this->totalProductMass += $mass;
            $this->totalValue += $value;
            $currency = $product["value"]["currency"];
            
            if (isset($this->totalValueCurrency) and $this->totalValueCurrency != $product["value"]["currency"]) {
                throw new Exception("You can't mix currencies for products");
            } else {
                $this->totalValueCurrency = $product["value"]["currency"];
            }
            
            if ($key<4) {
                $descfont = 8;
                $this->pdf->SetXY(26.5,$this->formPosition[$key]);
                while ($this->pdf->GetStringWidth($product["name"]) > self::DESCWIDTH) {
                    $descfont--;
                    $this->pdf->SetFont('Courier','',$descfont);
                }
                $this->pdf->SetFont('Courier','',$descfont);
                $this->pdf->Cell(73,0,$product["name"],0,0);
                $this->pdf->SetFont('Courier','',8);
                $this->pdf->Cell(31.5,0,$product["salesQty"],0,0);
                $this->pdf->SetFont('Courier','',8);
                $this->pdf->Cell(31.5,0,$mass,0,0);
                $this->pdf->SetFont('Courier','',8);
                $this->pdf->Cell(31.5,0,round($value,2).$currency,0,0);
                if ($this->goodsType==self::MERCHANDISE or $this->goodsType==self::COMMERCIALSAMPLE or $this->goodsType==self::OTHER) {
                    $this->pdf->SetFont('Courier','',8);
                    $this->pdf->Cell(44,0,substr($product["intrastat_code"],0,6),0,0);
                    $this->pdf->SetFont('Courier','',8);
                    $this->pdf->Cell(43,0,$product["origin_country"],0,0);
                }
            } else {
                $this->overrun[]=$product;
            }
            
        }
    }  
    
    protected function WriteOverrun() {
        $this->pdf->SetXY(26.5,132);
        $this->pdf->Cell(73,0,"Continued on additional page(s)",0,0);
        
        $this->pdf->AddPage();
        
        $this->pdf->SetXY(26.5,20);
        $this->pdf->Cell(73,0,"Detailed description of contents",0,0);
        $this->pdf->SetFont('Courier','',8);
        $this->pdf->Cell(31.5,0,"Quantity",0,0);
        $this->pdf->SetFont('Courier','',8);
        $this->pdf->Cell(31.5,0,"Net Weight",0,0);
        $this->pdf->SetFont('Courier','',8);
        $this->pdf->Cell(31.5,0,"Value",0,0);
        if ($this->goodsType==self::MERCHANDISE or $this->goodsType==self::COMMERCIALSAMPLE or $this->goodsType==self::OTHER) {
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(44,0,"HS tariff number",0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(43,0,"Country of origin of goods",0,0);
        }
        
        foreach ($this->overrun as $product) {
            $this->pdf->SetXY(26.5,$this->pdf->GetY()+8);
            $mass = $product["itemMass"]*$product["salesQty"];
            $value = $product["value"]["value"]*$product["salesQty"];
            $descfont = 8;
            while ($this->pdf->GetStringWidth($product["name"]) > self::DESCWIDTH) {
                $descfont--;
                $this->pdf->SetFont('Courier','',$descfont);
            }
            $this->pdf->SetFont('Courier','',$descfont);
            $this->pdf->Cell(73,0,$product["name"],0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(31.5,0,$product["salesQty"],0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(31.5,0,$mass,0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(31.5,0,round($value,2).$product["value"]["currency"],0,0);
            if ($this->goodsType==self::MERCHANDISE or $this->goodsType==self::COMMERCIALSAMPLE or $this->goodsType==self::OTHER) {
                $this->pdf->SetFont('Courier','',8);
                $this->pdf->Cell(44,0,substr($product["intrastat_code"],0,6),0,0);
                $this->pdf->SetFont('Courier','',8);
                $this->pdf->Cell(43,0,$product["origin_country"],0,0);
            }
        }
    }
}
