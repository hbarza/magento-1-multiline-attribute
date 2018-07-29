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

class Codnitive_MultilineAttr_Model_Search_List extends Varien_Object
{
    
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        
        $config = Mage::getModel('multilineattr/config');
        $helper = Mage::helper('multilineattr');
        $list   = Mage::getModel('multilineattr/list');
        $type   = $this->getType();
        $typeId = $list->getAttributeId($type);
        $query  = $this->getQuery();
        
        $searchType = $config->getSearchType();
        switch ($searchType) {
            case 'global':
                $like = '%' . $query . '%';
                break;
            
            case 'from_left':
                $like = $query . '%';
                break;
            
            default:
                $like = $query;
                break;
        }
        
        $searchMainTable = $config->searchMainTable();
        $titleField = 'title';
        $typeField  = 'type';
        if ($searchMainTable) {
            $collection = Mage::getModel('multilineattr/multiline')->getCollection();
            $titleField = 'value';
            $typeField  = 'attribute_id';
        }
        else {
            $collection = Mage::getModel('multilineattr/list')->getCollection();
        }
            $collection->addFieldToFilter($titleField, array('like' => $like));
        if ($config->showCurrentType()) {
            $collection->addFieldToFilter($typeField, $typeId);
        }
        $collection->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        
        foreach ($collection as $title) {
            $titleType = $searchMainTable ? $title->getAttributeId() : $title->getType();
            $typeCode = $list->getAttributeCode($titleType);
            $attributeModel = Mage::getModel('eav/entity_attribute')->load($titleType);
            $arr[] = array(
                'id'    => $typeCode.'_' . $title->getId(),
                'type'  => $helper->__(ucwords(str_replace('_', ' ', $typeCode))),
                'label' => $attributeModel->getFrontendLabel(),
                'name'  => $searchMainTable ? $title->getValue() : $title->getTitle(),
            );
        }
        
        $btnShowAlways = $config->addButtonShowType();
        if (empty($arr) || $btnShowAlways == 'always') {
            $attributeModel = Mage::getModel('eav/entity_attribute')->load($type, 'attribute_code');
            $arr[] = array(
                'id'         => empty($arr) ? $type.'_not_exists' : 'add_'.$type,
                'type'       => $helper->__($type),
                'label'      => $attributeModel->getFrontendLabel(),
                'name'       => $query,
                'button'     => true,
                'not_exists' => empty($arr) ? true : false,
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
