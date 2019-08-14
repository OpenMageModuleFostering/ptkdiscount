<?php
class PTK_Discount_Model_Catalog_Product extends Mage_Catalog_Model_Product{



    public function getSpecialPrice(){
        $model = Mage::getModel("discount/discount");
        $percentage = $model->getPercentage($this->getId());
        if(!empty($percentage)){
            $price = $this->getPrice();
            return $price -  ( $price * $percentage / 100);
        }else{
            return null;
        }
    }


    public function getFinalPrice($qty=null)
    {
        $price = $this->getSpecialPrice();
        if ($price !== null) {
            return $price;
        }
        return $this->getPriceModel()->getFinalPrice($qty, $this);
    }


}
		