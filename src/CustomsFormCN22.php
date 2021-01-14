<?php namespace fhindsthejewellers\GBCustomsForm;

class CustomsFormCN22 extends CustomsForm {
    private $formPosition = array (83,87,91,95,99,103,107);
    protected $totalQty=0;
    const DESCWIDTH = 52;
    
    public function __construct(array $nonProductDetails) {
        $this->pdf = new \FPDF('P','mm',array(100,160));
        $this->pdf->AddPage();
        $this->pdf->image(__DIR__."/CN22B.png",0,2,100);
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetMargins(1,0);
        $this->pdf->SetAutoPageBreak(false,0);
        parent::__construct($nonProductDetails);
        
        if (isset($nonProductDetails["fromAddress"])) {
            $this->fromAddress= new Address($nonProductDetails["fromAddress"]);
        } else {
            throw new \Exception("fromAddress is missing");
        }
    }
    
    public function BuildForm(array $products) {
        parent::BuildForm($products);
        $this->WriteTotalQty();
        $this->WriteVatNumber();
    }
    
    protected function WriteAddresses() {
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetXY(30,36);
        if (isset($this->fromAddress->name)) {$this->pdf->Cell(30,0,$this->fromAddress->name,0,0);}
        //$this->pdf->SetXY(30,36);
        if (isset($this->fromAddress->business)) {$this->pdf->Cell(2,0,$this->fromAddress->business,0,0);}
        $this->pdf->SetXY(30,44);
        $this->pdf->Cell(2,0,$this->fromAddress->street,0,0);
        $this->pdf->SetXY(30,48);
        $this->pdf->Cell(2,0,$this->fromAddress->zip,0,0);
        $this->pdf->SetXY(30,52);
        $this->pdf->Cell(2,0,$this->fromAddress->city,0,0);
        $this->pdf->SetXY(30,56);
        $this->pdf->Cell(2,0,$this->fromAddress->country,0,0);
        
    }
        
    protected function TickGoodsBox() {
        switch ($this->goodsType) {
            case self::GIFT:
                $this->pdf->SetXY(31,66);
                break;
            case self::DOCUMENTS:
                $this->pdf->SetXY(31,70);
                break;
            case self::COMMERCIALSAMPLE:
                $this->pdf->SetXY(59,66);
                break;
            case self::RETURNEDGOODS:
                $this->pdf->SetXY(59,70);
                break;
            case self::MERCHANDISE:
                $this->pdf->SetXY(31,75); //75
                break;
            default:
                $this->pdf->SetXY(59,75);
                break;
        }
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->Cell(2,0,'X',0,0);
    }
    
    protected function SignAndDate() {
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetXY(15,143);
        $this->pdf->Cell(80,0,$this->date.' '.$this->name,0,0);
    }
    
    protected function WriteTotalWeight() {
        if ($this->parcelMass > $this->totalProductMass) {
            $mass = $this->parcelMass;
        } else {
            $mass=$this->totalProductMass;
        }
        $this->pdf->SetXY(68,110);
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->Cell(14,0,$mass,0,0);
    }
    
    protected function WriteTotalValue() {
        $this->pdf->SetXY(83,107);
        $this->pdf->SetFont('Courier','',8);
        $this->pdf->Cell(14,5,round($this->totalValue,2),0,2);
        $this->pdf->Cell(14,0,$this->totalValueCurrency,0,0);
    }
    
    protected function WriteTotalQty() {
        $this->pdf->SetXY(53.5,107);
        $this->pdf->SetFont('Courier','',8);
        $this->pdf->Cell(15,5,round($this->totalQty,2),0,2,"C");
    }
    
    protected function WriteVatNumber() {
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetXY(51,125);
        $this->pdf->Cell(45,0,$this->vatNumber,0,0);
    }
    
    protected function WriteProducts(array $products) {
        $originHs1 = "";
        $originHs2 = "";
        $originHs3 = "";
        $hsCode= "";
        $origin = "";
        $currency = "GBP";
        foreach ($products as $key=>$product) {
            $descfont = 8;
            $this->pdf->SetXY(1.5,$this->formPosition[$key]);
            $this->pdf->SetFont('Courier','',$descfont);
            $index = $key + 1;
            $description = "{$product["name"]}";
            while ($this->pdf->GetStringWidth($description) > self::DESCWIDTH) {
                $descfont--;
                $this->pdf->SetFont('Courier','',$descfont);
            }
            //echo $this->pdf->GetStringWidth($description) . PHP_EOL;
            $mass = $product["itemMass"]*$product["salesQty"];
            $value = $product["value"]["value"]*$product["salesQty"];
            $this->totalProductMass += $mass;
            $this->totalValue += $value;
            
            $this->totalQty += (int) $product["salesQty"];
            
            if (isset($this->totalValueCurrency) and $this->totalValueCurrency != $product["value"]["currency"]) {
                throw new \Exception("You can't mix currencies for products");
            } else {
                $this->totalValueCurrency = $product["value"]["currency"];
            }
            
            if ($key!=0){
                $origin = $origin.', ';
                $hsCode = $hsCode.',';
            }
            $hsCode = substr($product["intrastat_code"],0,6);
            if ($key==0 or $key==1){
                $originHs1 = $originHs1 . "$index:({$hsCode};{$product["origin_country"]}) ";
            } elseif ($key==2 or $key==3) {
                $originHs2 = $originHs2 . "$index:({$hsCode};{$product["origin_country"]}) ";
            }  else {
                $originHs3 = $originHs3 . "$index:({$hsCode};{$product["origin_country"]}) ";
            }
            $this->pdf->Cell(52,1,$description,0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(15,1,$product["salesQty"],0,0,"C");
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(14,0,$mass,0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(15,0,round($value,2).$product["value"]["currency"],0,0);
            
        }
        $this->pdf->SetXY(51,115);
        $this->pdf->SetFont('Courier','',8);
        $this->pdf->Cell(46,0,$originHs1,0,0);
        $this->pdf->SetXY(51,117.75);
        $this->pdf->Cell(46,0,$originHs2,0,0);
        $this->pdf->SetXY(51,120.5);
        $this->pdf->Cell(46,0,$originHs3,0,0);
        


    }
    
  
}
