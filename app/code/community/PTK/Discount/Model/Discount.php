<?php

class PTK_Discount_Model_Discount extends Mage_Core_Model_Abstract
{

    var $prefijo;


    protected function _construct(){
       $this->_init("discount/discount");
       $this->prefijo = (string) Mage::getConfig()->getTablePrefix();
    }


    /**
     * Metodo que retorna el procentaje de descuento de un producto, basandoce en el modulo de descuentos
     *
     * @param $product_id
     * @return null | int
     */
    public function getPercentage($product_id){

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $customer = Mage::getModel('customer/customer')->load($customerData->getId());
            $group_id = $customer->getGroupId();
        }else{
            $group_id = 0;
        }

        $sql = 'SELECT percentage
                FROM  `'.$this->prefijo.'ptk_discount`
                WHERE product_id = '.(int)$product_id.'
                AND NOW() BETWEEN `from` and `to`
                AND (group_user_id = -1  OR group_user_id = '.$group_id.' )
                ORDER BY created DESC
                LIMIT 1';
        return  $readConnection->fetchOne($sql);

    }



    /**
     * Metodo que registra los descuentos
     *
     * @param array $register
     */
    public function saveRowQuery($register=array()){

       if(count($register) != 6){
          return false;
       }


        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        $sql = 'INSERT INTO  `'.$this->prefijo.'ptk_discount` (
                    `product_id` ,
                    `percentage` ,
                    `from` ,
                    `to`,
                    `group_user_id`
                    )
                    VALUES (
                    "'.$register['product_id'].'"  ,"'.$register[0].'", "'.$register[2].'",   "'.$register[3].'", "'.(int)$register[4].'" )';
        $writeConnection->query($sql);
        return true;
    }

    /**
     * Metodo que permite limpiar la tabla
     */
    public function truncateTable(){
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $sql = 'TRUNCATE '.$this->prefijo.'ptk_discount';
        $writeConnection->query($sql);

    }

    /**
     * Metodo que busca un producto por su sku y retorna el id
     *
     * @param string $sku
     * @return mixed
     */
    public function getProductId($sku=''){
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $sql = 'SELECT entity_id FROM '.$this->prefijo.'catalog_product_entity WHERE STRCMP(sku, "' . trim($sku) . '")  = 0';
        return $readConnection->fetchOne($sql);
    }


}
	 