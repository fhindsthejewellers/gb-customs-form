<?php namespace fhindsthejewellers\GBCustomsForm;

class CustomsManager {
    const MAXCN22PRODUCTS = 4;
    const MAXCN22VALUE = 270;
    const MAXCN22NAMECHAR = 35;
    const MAXCN23NAMECHAR = 55;
    
    protected $type;
    protected $form;
    
    public function __construct(array $productArray,array $nonProductDetails) {
        if(self::IsCn22FormPossible($productArray)){
            $this->type="cn22";
            $this->form=new CustomsFormCN22($nonProductDetails);
        } else {
            $this->type="cn23";
            $this->form=new CustomsFormCN23($nonProductDetails);
        } 
        $this->form->BuildForm($productArray);
    }
    
    public function GetType() : string {
        return $this->type;
    }
    
    public function GetPDF() : \FPDF {
        return $this->form->GetPDF();
    }
    
    static public function ValidateProductArray(array $productArray) : array {
        $cn22 = self::IsCn22FormPossible($product);
        
        if ($cn22) {
            foreach ($productArray["products"] as $key=>$product) {
                $productArray["products"][$key]["valid"] = true;
                if (strlen($product["name"]) > self::MAXCN22NAMECHAR) {
                    $productArray["products"][$key]["valid"] = false;
                    $productArray["products"][$key]["message"] = "NameTooLong";
                    $productArray["products"][$key]["maxlength"] = self::MAXCN22NAMECHAR;
                    
                }
            }
        } else {
            foreach ($productArray as $key=>$product) {
                $productArray["products"][$key]["valid"] = true;
                if (strlen($product["name"]) > self::MAXCN23NAMECHAR) {
                    $productArray["products"][$key]["valid"] = false;
                    $productArray["products"][$key]["message"] = "NameTooLong";
                    $productArray["products"][$key]["maxlength"] = self::MAXCN23NAMECHAR;
                }
            }
        }
        return $productArray;
        
    }
    
    static public function IsCn22FormPossible(array $productArray) : bool {
        $cn22=false;
        if (sizeof($productArray)<=self::MAXCN22PRODUCTS) {
            $value = 0;
            foreach ($productArray as $product) {
                $value += ($product["value"]["value"]/$product["value"]["exchangeRate"]) * $product["salesQty"];
            }
            if ($value<=self::MAXCN22VALUE) {
                $cn22=true;
            }
        }
        return $cn22;
    }
}
