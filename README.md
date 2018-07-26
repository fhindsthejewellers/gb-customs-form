GB Customs Forms
=========================

This library is under development and the interface is not stable!

This library produces FPDF objects containing CN22 or CN23 forms.

Have a look in the examples folder.

The basic syntax is as follows:
```
$form = new \fhindsthejewellers\GBCustomsForm\CustomsManager($products,$details);

$type = $form->GetType();

$pdf = $form->GetPDF();
```
Where $products is an array of products and $details is an array of details about the parcel

The CustomsManager class also provides a couple of static methods:

* IsCn22FormPossible(array) : bool - Tells you if these products will allow you to make the simplar CN22 form. CN23 needs more information passed in the $details array.
* ValidateProductArray(array) : array - This will pass back your product array and tell you if the names are too long

