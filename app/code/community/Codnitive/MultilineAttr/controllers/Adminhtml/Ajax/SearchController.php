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

class Codnitive_MultilineAttr_Adminhtml_Ajax_SearchController extends Mage_Adminhtml_Controller_Action 
{
    
    public function listAction()
    {
        $searchModule = Mage::getConfig()->getNode("adminhtml/attribute_search/list");
        $items = array();
        
        if (empty($searchModule)) {
            $items[] = array(
                'id' => 'error',
                'type' => Mage::helper('multilineattr')->__('Error'),
                'name' => Mage::helper('multilineattr')->__('No search modules were registered'),
                'description' => Mage::helper('multilineattr')->__('Please make sure that all global admin search modules are installed and activated.')
            );
            $totalCount = 1;
        } else {
            $searchResults = Mage::getModel('multilineattr/config')->getMaxResults();
            $start = $this->getRequest()->getParam('start', 1);
            $limit = $this->getRequest()->getParam('limit', $searchResults);
            $query = $this->getRequest()->getParam('query', '');
                
            $className = $searchModule->getClassName();
            $searchInstance = new $className();
            $results = $searchInstance
                ->setStart($start)
                ->setLimit($limit)
                ->setQuery($query)
                ->setType($this->getRequest()->getParam('type'))
                ->load()
                ->getResults();
            $items = array_merge_recursive($items, $results);
            
            $totalCount = sizeof($items);
        }
        
        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('codnitive/multilineattr/search/ajax/autocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function addToListAction()
    {
        $listModel = Mage::getModel('multilineattr/list');
        $type = $this->getRequest()->getParam('type');
        $typeId = $listModel->getAttributeId($type);
        $value = $this->getRequest()->getParam('value');
        $item = array(
            'type' => $type
        );
        
        $list  = Mage::getModel('multilineattr/list');
        $collection = $list->getCollection()
            ->addFieldToFilter('title', $value)
            ->addFieldToFilter('type', $typeId)
            ->getData();
        
        $inArray = false;
        foreach ($collection as $pub) {
            if (in_array(strtolower($value), array_map('strtolower', $pub))) {
                $inArray = true;
                break;
            }
        }
        
        $attributeModel = Mage::getModel('eav/entity_attribute')->load($type, 'attribute_code');
        if (!$inArray) {
            $list->setTitle($value)
                    ->setType($typeId)
                    ->save();
            $item['success'] = $list->getTitle() ? true : false;
            $item['title']   = $value;
            $item['label']   = $attributeModel->getFrontendLabel();
        }
        else {
            $item['success'] = true;
            $item['exists']  = true;
            $item['title']   = $value;
            $item['label']   = $attributeModel->getFrontendLabel();
        }
        
        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('codnitive/multilineattr/search/ajax/additemresponse.phtml')
            ->assign('item', $item);
        
        $this->getResponse()->setBody($block->toHtml());
    }
    
}