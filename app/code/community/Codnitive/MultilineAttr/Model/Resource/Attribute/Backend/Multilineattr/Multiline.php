<?php
/**
 * CODNITIVE
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE_EULA.html.
 * It is also available through the world-wide-web at this URL:
 * http://www.codnitive.com/en/terms-of-service-softwares/
 * http://www.codnitive.com/fa/terms-of-service-softwares/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   Codnitive
 * @package    Codnitive_MultilineAttr
 * @author     Hassan Barza <support@codnitive.com>
 * @copyright  Copyright (c) 2012 CODNITIVE Co. (http://www.codnitive.com)
 * @license    http://www.codnitive.com/en/terms-of-service-softwares/ End User License Agreement (EULA 1.0)
 */

class Codnitive_MultilineAttr_Model_Resource_Attribute_Backend_Multilineattr_Multiline extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('multilineattr/multiline', 'value_id');
    }
    
    public function loadProductData($product, $attribute)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array(
                'website_id',
                'value'
            ))
            ->where('entity_id = ?', (int)$product->getId())
            ->where('attribute_id = ?', (int)$attribute->getId());
        if ($attribute->isScopeGlobal()) {
            $select->where('website_id = ?', 0);
        } else {
            $storeId = $product->getStoreId();
            if ($storeId) {
                $select->where('website_id IN (?)', array(0, Mage::app()->getStore($storeId)->getWebsiteId()));
            }
        }
        return $this->_getReadAdapter()->fetchAll($select);
    }

    public function deleteProductData($product, $attribute)
    {
        $where = array(
            'entity_id = ?'    => (int)$product->getId(),
            'attribute_id = ?' => (int)$attribute->getId()
        );

        $adapter   = $this->_getWriteAdapter();
        if (!$attribute->isScopeGlobal()) {
            $storeId = $product->getStoreId();
            if ($storeId) {
                $where['website_id IN(?)'] =  array(0, Mage::app()->getStore($storeId)->getWebsiteId());
            }
        }
        $adapter->delete($this->getMainTable(), $where);
        return $this;
    }

    public function insertProductData($product, $data)
    {
        $data['entity_id']      = (int)$product->getId();
        $data['entity_type_id'] = (int)$product->getEntityTypeId();

        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }
}

