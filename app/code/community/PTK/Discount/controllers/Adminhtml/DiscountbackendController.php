<?php
class PTK_Discount_Adminhtml_DiscountbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction(){

       $this->loadLayout();
	   $this->_title($this->__("Import"));
	   $this->renderLayout();
    }

	protected function _isAllowed(){
		return Mage::getSingleton('admin/session')->isAllowed('discount/discountbackend');
	}


}