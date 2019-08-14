<?php
class PTK_Discount_Block_Adminhtml_Discount_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("discount_form", array("legend"=>Mage::helper("discount")->__("Discount information")));


						$fieldset->addField("sku", "text", array(
							"label" => Mage::helper("discount")->__("Product SKU"),
							"name" => "sku",
							'required'  => true
						));

						$fieldset->addField("product_id", "hidden", array(
						"label" => Mage::helper("discount")->__("Product ID"),
						"name" => "product_id",
						));

						$fieldset->addField("precio", "hidden", array(
							"label" => Mage::helper("discount")->__("precio"),
							"name" => "precio",
						));

						$fieldset->addField("percentage", "text", array(
							"label" => Mage::helper("discount")->__("Percentage"),
							"name" => "percentage",
							'required'  => true,
						));

						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('from', 'date', array(
						'label'        => Mage::helper('discount')->__('From'),
						'name'         => 'from',
						'required'  => true,
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso,
						'class' => 'js-start'
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('to', 'date', array(
						'label'        => Mage::helper('discount')->__('To'),
						'name'         => 'to',
						'required'  => true,
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso,
						'class' => 'js-end'
						));				
						 $fieldset->addField('group_user_id', 'select', array(
						'label'     => Mage::helper('discount')->__('Customer Group'),
						'values'   => PTK_Discount_Block_Adminhtml_Discount_Grid::getValueArray3(),
						'name' => 'group_user_id'

						));

				if (Mage::getSingleton("adminhtml/session")->getDiscountData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getDiscountData());
					Mage::getSingleton("adminhtml/session")->setDiscountData(null);
				} 
				elseif(Mage::registry("discount_data")) {
				    $form->setValues(Mage::registry("discount_data")->getData());
				}
				return parent::_prepareForm();
		}
}
