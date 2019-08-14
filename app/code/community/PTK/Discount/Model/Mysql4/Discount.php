<?php
class PTK_Discount_Model_Mysql4_Discount extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct(){
        $this->_init("discount/discount", "entity_id");
    }

    protected function _getLoadSelect($field, $value, $object){

        $select = parent::_getLoadSelect($field, $value, $object);

        $select->joinLeft(
            array('t_b' => 'catalog_product_entity'),
            $this->getMainTable() . '.product_id = t_b.entity_id',
            array('sku' => 'sku'));
        return $select;
    }

}