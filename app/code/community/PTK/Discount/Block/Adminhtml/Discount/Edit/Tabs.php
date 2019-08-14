<?php
class PTK_Discount_Block_Adminhtml_Discount_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("discount_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("discount")->__("Discount Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("discount")->__("Information"),
				"title" => Mage::helper("discount")->__("Discount Information"),
				"content" => $this->getLayout()->createBlock("discount/adminhtml_discount_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
