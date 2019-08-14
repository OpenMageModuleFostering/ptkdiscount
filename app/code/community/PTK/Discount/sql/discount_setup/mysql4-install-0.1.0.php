<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
        DROP TABLE IF EXISTS {$this->getTable('discount/discount')};
           CREATE TABLE `{$this->getTable('discount/discount')}` (
			`entity_id` Int( 10 ) AUTO_INCREMENT NOT NULL COMMENT 'key',
			`product_id` Int( 10 ) NOT NULL,
			`percentage` Int( 10 ) NOT NULL,
			`user_id` Int( 10 ) NOT NULL,
			`from` Timestamp NOT NULL,
			`to` Timestamp NOT NULL,
			`group_user_id` Int( 10 ) NOT NULL,
			`created` Timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY ( `entity_id` ) )
		COMMENT 'Gestiona los paramatros de configuración para los descuentos de los productos'
		ENGINE = InnoDB;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 