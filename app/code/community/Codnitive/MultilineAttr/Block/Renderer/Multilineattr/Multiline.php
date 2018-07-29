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

class Codnitive_MultilineAttr_Block_Renderer_Multilineattr_Multiline 
    extends Mage_Adminhtml_Block_Widget 
    implements Varien_Data_Form_Element_Renderer_Interface
{

    protected $_element = null;
    protected $_websites = null;

    public function __construct()
    {
        $this->setTemplate('codnitive/multilineattr/renderer/multiline.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $this->_setAddButton();
        return $this->toHtml();
    }

    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function getValues()
    {
        return $this->getElement()->getValue();
    }

    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    public function isMultiWebsites()
    {
        return !Mage::app()->isSingleStoreMode();
    }

    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name'      => $this->__('All Websites'),
            'currency'  => Mage::app()->getBaseCurrencyCode()
        );

        if (!Mage::app()->isSingleStoreMode() && !$this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            if ($storeId = $this->getProduct()->getStoreId()) {
                $website = Mage::app()->getStore($storeId)->getWebsite();
                $websites[$website->getId()] = array(
                    'name'      => $website->getName(),
                    'currency'  => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                );
            } else {
                foreach (Mage::app()->getWebsites() as $website) {
                    if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                        continue;
                    }
                    $websites[$website->getId()] = array(
                        'name'      => $website->getName(),
                        'currency'  => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                    );
                }
            }
        }
        $this->_websites = $websites;
        return $this->_websites;
    }

    protected function _setAddButton()
    {
        $name = $this->getElement()->getLabel();
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add %s', $name),
                    'onclick'   => "multilineControl.addItem('".$this->getElement()->getHtmlId()."')",
                    'class' => 'add'
                )));
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    public function getMinChars()
    {
        return Mage::getModel('multilineattr/config')->getSearchMinChars();
    }
    
    public function isAjaxSearchEnabled()
    {
        return Mage::getModel('multilineattr/config')->isEnabled();
    }
    
    public function isAllowedAttr($attr)
    {
        $allowedAttr = Mage::getModel('multilineattr/config')->getActiveAttr();
        if (in_array($attr, $allowedAttr)) {
            return true;
        }
        return false;
    }

}
