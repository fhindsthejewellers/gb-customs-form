<?php namespace fhindsthejewellers\GBCustomsForm;

class CustomsFormCN22 extends CustomsForm {
    private $formPosition = array (41,47,53,59);
    
    const DESCWIDTH = 57;
    
    public function __construct(array $nonProductDetails) {
        $this->pdf = new \FPDF('P','mm',array(100,105));
        $this->pdf->AddPage();
        $this->pdf->image(__DIR__."/cn22.png",2,5,96);
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetMargins(1,0);
        $this->pdf->SetAutoPageBreak(false,0);
        parent::__construct($nonProductDetails);
    }
        
    protected function TickGoodsBox() {
        switch ($this->goodsType) {
            case self::GIFT:
                $this->pdf->SetXY(7,26);
                break;
            case self::DOCUMENTS:
                $this->pdf->SetXY(7,30);
                break;
            case self::COMMERCIALSAMPLE:
                $this->pdf->SetXY(38,26);
                break;
            default:
                $this->pdf->SetXY(38,30);
                break;
        }
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->Cell(2,0,'X',0,0);
    }
    
    protected function SignAndDate() {
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->SetXY(35,94);
        $this->pdf->Cell(57,0,$this->date.' '.$this->name,0,0);
    }
    
    protected function WriteTotalWeight() {
        if ($this->parcelMass > $this->totalProductMass) {
            $mass = $this->parcelMass;
        } else {
            $mass=$this->totalProductMass;
        }
        $this->pdf->SetXY(61,77);
        $this->pdf->SetFont('Courier','',10);
        $this->pdf->Cell(19,0,$mass,0,0);
    }
    
    protected function WriteTotalValue() {
        $this->pdf->SetXY(80,72);
        $this->pdf->SetFont('Courier','',8);
        $this->pdf->Cell(15,5,$this->totalValue,0,2);
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
            $this->pdf->SetXY(4,$this->formPosition[$key]);
            $this->pdf->SetFont('Courier','',$descfont);
            $index = $key + 1;
            $description = "$index: {$product["name"]} x{$product["salesQty"]}";
            while ($this->pdf->GetStringWidth($description) > self::DESCWIDTH) {
                $descfont--;
                $this->pdf->SetFont('Courier','',$descfont);
            }
            //echo $this->pdf->GetStringWidth($description) . PHP_EOL;
            $mass = $product["itemMass"]*$product["salesQty"];
            $value = $product["unitGrossPaid"]*$product["salesQty"];
            $this->totalProductMass += $mass;
            $this->totalValue += $value;
            
            if (isset($this->totalValueCurrency) and $this->totalValueCurrency != $product["currency"]) {
                throw new \Exception("You can't mix currencies for products");
            } else {
                $this->totalValueCurrency = $product["currency"];
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
            $this->pdf->Cell(57,0,$description,0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(19,0,$mass,0,0);
            $this->pdf->SetFont('Courier','',8);
            $this->pdf->Cell(15,0,$value.$product["currency"],0,0);
            
        }
        $this->pdf->SetXY(4,74);
        $this->pdf->SetFont('Courier','',9);
        $this->pdf->Cell(57,0,$originHs1,0,0);
        $this->pdf->SetXY(4,78);
        $this->pdf->Cell(57,0,$originHs2,0,0);

    }
    
    
}
