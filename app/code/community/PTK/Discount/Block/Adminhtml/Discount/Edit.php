<?php
	
class PTK_Discount_Block_Adminhtml_Discount_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "entity_id";
				$this->_blockGroup = "discount";
				$this->_controller = "adminhtml_discount";
				$this->_updateButton("save", "label", Mage::helper("discount")->__("Save Discount"));
				$this->_updateButton("delete", "label", Mage::helper("discount")->__("Delete Discount"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("discount")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);

			    $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
		   	    $currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();


				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}

							window.baseUrlDiscount = '".Mage::helper("adminhtml")->getUrl('*/adminhtml_discount/findProduct/')."'
							window.baseUrlDiscountDate = '".Mage::helper("adminhtml")->getUrl('*/adminhtml_discount/startdate/')."'
							window.currency = '".$currency_symbol."'
							window.errorFecha = '".Mage::helper('discount')->__('The From Date value should be less than or equal to the To Date value.')."'
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("discount_data") && Mage::registry("discount_data")->getId() ){

				    return Mage::helper("discount")->__("Edit Discount '%s'", $this->htmlEscape(Mage::registry("discount_data")->getId()));

				} 
				else{

				     return Mage::helper("discount")->__("Add Discount");

				}
		}
}