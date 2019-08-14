<?php


class PTK_Discount_Block_Adminhtml_Discount extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_discount";
	$this->_blockGroup = "discount";
	$this->_headerText = Mage::helper("discount")->__("Discount Manager");
	$this->_addButtonLabel = Mage::helper("discount")->__("Add New Discount");
	parent::__construct();
	
	}

}