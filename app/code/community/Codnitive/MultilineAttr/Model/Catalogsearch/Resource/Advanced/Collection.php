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


/**
 * Collection Advanced
 *
 * @category   Codnitive
 * @package    Codnitive_MultilineAttr
 * @author     Hassan Barza <support@codnitive.com>
 */
class Codnitive_MultilineAttr_Model_Catalogsearch_Resource_Advanced_Collection 
    extends Mage_CatalogSearch_Model_Resource_Advanced_Collection
{
    /**
     * Add not indexable fields to search
     *
     * @param array $fields
     * @return Mage_CatalogSearch_Model_Resource_Advanced_Collection
     */
    public function addFieldsToFilter($fields)
    {
        if ($fields) {
            $previousSelect = null;
            $conn = $this->getConnection();
            foreach ($fields as $table => $conditions) {
                foreach ($conditions as $attributeId => $conditionValue) {
                    
                    $entity = 'entity_id';
                    $store  = 'store_id';
                    if ($table == 'codnitive_attribute_multiline') {
                        $attributeId = $this->_getAttributeId($attributeId);
                        $entity = 'value_id';
                        $store  = 'website_id';
                    }
                    
                    $select = $conn->select();
                    $select->from(array('t1' => $table), 'entity_id');
                    $conditionData = array();

                    if (!is_numeric($attributeId)) {
                        $field = 't1.'.$attributeId;
                    }
                    else {
                        $storeId = $this->getStoreId();
                        $onCondition = "t1.{$entity} = t2.{$entity}"
                                . ' AND t1.attribute_id = t2.attribute_id'
                                . " AND t2.{$store}=?";

                        $select->joinLeft(
                            array('t2' => $table),
                            $conn->quoteInto($onCondition, $storeId),
                            array()
                        );
                        $select->where("t1.{$store} = ?", 0);
                        $select->where('t1.attribute_id = ?', $attributeId);

                        if (array_key_exists('price_index', $this->getSelect()->getPart(Varien_Db_Select::FROM))) {
                            $select->where('t1.entity_id = price_index.entity_id');
                        }

                        $field = $this->getConnection()->getCheckSql('t2.value_id>0', 't2.value', 't1.value');

                    }

                    if (is_array($conditionValue)) {
                        if (isset($conditionValue['in'])){
                            $conditionData[] = array('in' => $conditionValue['in']);
                        }
                        elseif (isset($conditionValue['in_set'])) {
                            $conditionParts = array();
                            foreach ($conditionValue['in_set'] as $value) {
                                $conditionParts[] = array('finset' => $value);
                            }
                            $conditionData[] = $conditionParts;
                        }
                        elseif (isset($conditionValue['like'])) {
                            $conditionData[] = array ('like' => $conditionValue['like']);
                        }
                        elseif (isset($conditionValue['from']) && isset($conditionValue['to'])) {
                            $invalidDateMessage = Mage::helper('catalogsearch')->__('Specified date is invalid.');
                            if ($conditionValue['from']) {
                                if (!Zend_Date::isDate($conditionValue['from'])) {
                                    Mage::throwException($invalidDateMessage);
                                }
                                if (!is_numeric($conditionValue['from'])){
                                    $conditionValue['from'] = Mage::getSingleton('core/date')
                                        ->gmtDate(null, $conditionValue['from']);
                                    if (!$conditionValue['from']) {
                                        $conditionValue['from'] = Mage::getSingleton('core/date')->gmtDate();
                                    }
                                }
                                $conditionData[] = array('gteq' => $conditionValue['from']);
                            }
                            if ($conditionValue['to']) {
                                if (!Zend_Date::isDate($conditionValue['to'])) {
                                    Mage::throwException($invalidDateMessage);
                                }
                                if (!is_numeric($conditionValue['to'])){
                                    $conditionValue['to'] = Mage::getSingleton('core/date')
                                        ->gmtDate(null, $conditionValue['to']);
                                    if (!$conditionValue['to']) {
                                        $conditionValue['to'] = Mage::getSingleton('core/date')->gmtDate();
                                    }
                                }
                                $conditionData[] = array('lteq' => $conditionValue['to']);
                            }

                        }
                    } else {
                        $conditionData[] = array('eq' => $conditionValue);
                    }


                    foreach ($conditionData as $data) {
                        $select->where($conn->prepareSqlCondition($field, $data));
                    }

                    if (!is_null($previousSelect)) {
                        $select->where('t1.entity_id IN (?)', new Zend_Db_Expr($previousSelect));
                    }
                    $previousSelect = $select;
                }
            }
            $this->addFieldToFilter('entity_id', array('in' => new Zend_Db_Expr($select)));
        }

        return $this;
    }
    
    protected function _getAttributeId($code)
    {        
        $entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();
        
        $attributeId = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setEntityTypeFilter($entityTypeId)
                ->addFieldToFilter('attribute_code', $code)
                ->getFirstItem()
                ->getAttributeId();
        
        return $attributeId;
    }
    
}
