<?php namespace fhindsthejewellers\GBCustomsForm;

class CustomsFormCN22 extends CustomsForm {
    private $formPosition = array (42,48,54,60);
    
    const DESCWIDTH = 59;
    
    public function __construct(array $nonProductDetails) {
        $this->pdf = new \FPDF('P','mm',array(100,105));
        $this->pdf->AddPage();
        $this->pdf->image(__DIR__."/cn22.png",0,5,100);
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetMargins(1,0);
        $this->pdf->SetAutoPageBreak(false,0);
        parent::__construct($nonProductDetails);
    }
        
    protected function TickGoodsBox() {
        switch ($this->goodsType) {
            case self::GIFT:
                $this->pdf->SetXY(5,27);
                break;
            case self::DOCUMENTS:
                $this->pdf->SetXY(5,31);
                break;
            case self::COMMERCIALSAMPLE:
                $this->pdf->SetXY(37,26);
                break;
            default:
                $this->pdf->SetXY(37,31);
                break;
        }
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->Cell(2,0,'X',0,0);
    }
    
    protected function SignAndDate() {
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetXY(35,97);
        $this->pdf->Cell(60,0,$this->date.' '.$this->name,0,0);
    }
    
    protected function WriteTotalWeight() {
        if ($this->parcelMass > $this->totalProductMass) {
            $mass = $this->parcelMass;
        } else {
            $mass=$this->totalProductMass;
        }
        $this->pdf->SetXY(62,78);
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->Cell(19,0,$mass,0,0);
    }
    
    protected function WriteTotalValue() {
        $this->pdf->SetXY(82,74);
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
            $descfont = 8;
            $this->pdf->SetXY(2.3,$this->formPosition[$key]);
            $this->pdf->SetFont('Courier','',$descfont);
            $index = $key + 1;
            $description = "$index: {$product["name"]} x{$product["salesQty"]}";
            while ($this->pdf->GetStringWidth($description) > self::DESCWIDTH) {
                $descfont--;
                $this->pdf->SetFont('Courier','',$descfont);
            }
            //echo $this->pdf->GetStringWidth($description) . PHP_EOL;
            $mass = $product["itemMass"]*$product["salesQty"];
            $value = $product["value"]["value"]*$product["salesQty"];
            $this->totalProductMass += $mass;
            $this->totalValue += $value;
            
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
            } else {
                $originHs2 = $originHs2 . "$index:({$hsCode};{$product["origin_country"]}) ";
            }
            $this->pdf->Cell(59,1,$description,0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(20,0,$mass,0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(16,0,round($value,2).$product["value"]["currency"],0,0);
            
        }
        $this->pdf->SetXY(3,76.7);
        $this->pdf->SetFont('Courier','',9);
        $this->pdf->Cell(57,0,$originHs1,0,0);
        $this->pdf->SetXY(3,80.7);
        $this->pdf->Cell(57,0,$originHs2,0,0);

    }
    
    
}
