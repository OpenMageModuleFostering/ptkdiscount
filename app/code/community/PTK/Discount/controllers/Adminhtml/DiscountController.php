<?php

class PTK_Discount_Adminhtml_DiscountController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("discount/discount")->_addBreadcrumb(Mage::helper("adminhtml")->__("Discount  Manager"), Mage::helper("adminhtml")->__("Discount Manager"));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__("Discount"));
        $this->_title($this->__("Manager Discount"));

        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__("Discount"));
        $this->_title($this->__("Discount"));
        $this->_title($this->__("Edit Item"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("discount/discount")->load($id);
        if ($model->getId()) {
            Mage::register("discount_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("discount/discount");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Discount Manager"), Mage::helper("adminhtml")->__("Discount Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Discount Description"), Mage::helper("adminhtml")->__("Discount Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("discount/adminhtml_discount_edit"))->_addLeft($this->getLayout()->createBlock("discount/adminhtml_discount_edit_tabs"));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("discount")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {

        $this->_title($this->__("Discount"));
        $this->_title($this->__("Discount"));
        $this->_title($this->__("New Item"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("discount/discount")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("discount_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("discount/discount");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Discount Manager"), Mage::helper("adminhtml")->__("Discount Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Discount Description"), Mage::helper("adminhtml")->__("Discount Description"));


        $this->_addContent($this->getLayout()->createBlock("discount/adminhtml_discount_edit"))->_addLeft($this->getLayout()->createBlock("discount/adminhtml_discount_edit_tabs"));

        $this->renderLayout();

    }

    public function saveAction()
    {

        $post_data = $this->getRequest()->getPost();


        if ($post_data) {

            try {
                $model = Mage::getModel("discount/discount")
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Discount was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setDiscountData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setDiscountData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }

        }
        $this->_redirect("*/*/");
    }


    public function deleteAction()
    {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $model = Mage::getModel("discount/discount");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }


    public function massRemoveAction()
    {
        try {
            $ids = $this->getRequest()->getPost('entity_ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("discount/discount");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'discount.csv';
        $grid = $this->getLayout()->createBlock('discount/adminhtml_discount_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'discount.xml';
        $grid = $this->getLayout()->createBlock('discount/adminhtml_discount_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }


    public function findProductAction()
    {

        $post = $this->getRequest()->getPost();

        if (!empty($post['sku'])) {
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $post['sku']);
        } else {
            $product = null;
        }

        if ($product != null) {


            if (!$product->getImage() != 'no_selection' && $product->getImage()) {
                $img = (string)Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(250);
            } else {
                $img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/example/image.jpg';
            }
            $informacion = array(
                'sku' => $product->getSku(),
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'priceFormat' => Mage::helper('core')->currency($product->getPrice(), true, false),
                'img' => $img,
            );

        } else {
            $informacion = array();
        }

        echo json_encode($informacion);
        exit;

    }


    public function  startdateAction()
    {
        $post = $this->getRequest()->getPost();
        $date = Mage::getModel('core/date');
        $value = $date->timestamp($post['from']);
        $maxValue = $date->timestamp($post['to']);
        $informacion = array('message' => '');

        if ($value > $maxValue) {
            $message = Mage::helper('catalog')->__('The From Date value should be less than or equal to the To Date value.');
            $informacion = array('message' => $message);
        }
        echo json_encode($informacion);
        exit;
    }


    public function syncupAction()
    {

        $post_data = $this->getRequest()->getPost();
        if ($post_data && !empty($_FILES['file'])) {
            try {
                $model = Mage::getModel("discount/discount");
                if (isset($post_data['empty']) && $post_data['empty'] == 1) {
                    $model->truncateTable();
                }

                $originalDate = "2015/12/0";
                $newDate = date("Y-m-d H:i:s", strtotime($originalDate));
                $pathRead = Mage::getBaseDir('media') . DS . 'discount';
                $uploader = new Varien_File_Uploader('file');
                $uploader->setAllowedExtensions(array('csv'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $path = Mage::getBaseDir('media') . DS . 'discount' . DS;
                $uploader->save($path, $_FILES['file']['name']);
                $filename = $uploader->getUploadedFileName();

                $data = $this->getCsvData($pathRead . $filename);
                $resume = array();

                foreach ($data as $i => $row) {

                    $row = (is_array($row) and count($row) > 1) ? $row : explode(';', $row[0]);
                    foreach ($row as $i => $v) {
                        $row[$i] = str_replace('"', '', $v);
                    }

                    if (is_numeric($row[0])) {
                        $row['product_id'] = $model->getProductId($row[1]);


                        if ($row['product_id']) {
                            $row[2] = date("Y-m-d H:i:s", strtotime($row[2]));
                            $row[3] = date("Y-m-d H:i:s", strtotime($row[3]));
                            $model->saveRowQuery($row);
                        } else {
                            $resume[$i] = $row;
                            $resume[$i]['message'] = Mage::helper("discount")->__("No found product");

                        }
                    }


                }
                unlink($data);


            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setDiscountData($this->getRequest()->getPost());
                $this->_redirect("*/adminhtml_discountbackend/index", array("id" => $this->getRequest()->getParam("id")));
                return;
            }

            if (!empty($resume)) {
                Mage::getSingleton("adminhtml/session")->addNotice(Mage::helper("adminhtml")->__("Import complete witch errors"));
                //	$this->_redirect("*/Adminhtml_discountbackend/index");
            } else {
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Import complete"));
            }

            $this->_redirect("*/adminhtml_discount/index");


        }

    }


    public function getCsvData($file)
    {
        $csvObject = new Varien_File_Csv();
        try {
            return $csvObject->getData($file);
        } catch (Exception $e) {
            Mage::log('Csv: ' . $file . ' - getCsvData() error - ' . $e->getMessage(), Zend_Log::ERR, 'exception.log', true);
            return false;
        }

    }

}
