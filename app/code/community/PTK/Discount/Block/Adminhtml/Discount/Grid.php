<?php

class PTK_Discount_Block_Adminhtml_Discount_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    private $prefijo;

    public function __construct()
    {
        parent::__construct();
        $this->setId("discountGrid");
        $this->setDefaultSort("entity_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
        $this->prefijo = (string) Mage::getConfig()->getTablePrefix();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("discount/discount")->getCollection();

        // Add Column SKU
        $collection->getSelect()->join(
            array('s' => $collection->getTable('catalog/product')),
            'product_id = s.entity_id',
            array('sku' => 's.sku')
        );

        $collection->getSelect()->join(
            array('p' => $this->prefijo.'catalog_product_index_price'),
            'product_id = p.entity_id and (p.customer_group_id = 0 or p.customer_group_id = group_user_id )',
            array('price' => 'p.final_price', 'price_discount' => 'p.final_price'));


        $collection->getSelect()->columns(array('percentage' => new Zend_Db_Expr ('CONCAT(percentage,"%")')));
        $collection->getSelect()->columns(array('price_discount' => new Zend_Db_Expr ('price  -  ((percentage/100) * price)   ')));

        $collection->getSelect()->group('main_table.entity_id');
        $this->setCollection($collection);



        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $store = $this->_getStore();

        $this->addColumn("entity_id", array(
            "header" => Mage::helper("discount")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "entity_id",
        ));

        $this->addColumn("product_id", array(
            "header" => Mage::helper("discount")->__("Product ID"),
            "index" => "product_id",
        ));

        $this->addColumn("sku", array(
            "header" => Mage::helper("discount")->__("SKU"),
            "index" => "sku",
        ));


        $this->addColumn("percentage", array(
            "header" => Mage::helper("discount")->__("Percentage"),
            "index" => "percentage",
        ));

        $this->addColumn("price", array(
            "header" => Mage::helper("discount")->__("Price"),
            "index" => "price",
            'type'  => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
        ));

        $this->addColumn("price_discount", array(
            "header" => Mage::helper("discount")->__("Discount Price"),
            "index" => "price_discount",
            'type'  => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
        ));

        $this->addColumn('from', array(
            'header' => Mage::helper('discount')->__('From'),
            'index' => 'from',
            'type' => 'datetime',
        ));
        $this->addColumn('to', array(
            'header' => Mage::helper('discount')->__('To'),
            'index' => 'to',
            'type' => 'datetime',
        ));
        $this->addColumn('group_user_id', array(
            'header' => Mage::helper('discount')->__('Customer Groups'),
            'index' => 'group_user_id',
            'type' => 'options',
            'options' => PTK_Discount_Block_Adminhtml_Discount_Grid::getOptionArray3(),
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_discount', array(
            'label' => Mage::helper('discount')->__('Remove Discount'),
            'url' => $this->getUrl('*/adminhtml_discount/massRemove'),
            'confirm' => Mage::helper('discount')->__('Are you sure?')
        ));
        return $this;
    }

    static public function getOptionArray3()
    {
        $data = PTK_Discount_Block_Adminhtml_Discount_Grid::getValueArray3();
        foreach ($data as $j) {
            $data_array[$j['value']] = $j['label'];
        }

        return ($data_array);
    }

    static public function getValueArray3()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $prefix = Mage::getConfig()->getTablePrefix();

        $query = 'SELECT * FROM ' . $prefix . 'customer_group ORDER BY customer_group_code ASC';
        $data_array[] = array('value' => -1 , 'label' => Mage::helper('discount')->__('All'));
        $results = $readConnection->fetchAll($query);
        foreach ($results as $k => $v) {
            $data_array[] = array('value' => $v['customer_group_id'], 'label' => $v['customer_group_code']);
        }
        return ($data_array);

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }


}