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

class Codnitive_MultilineAttr_Model_Config
{
    
    const PATH_NAMESPACE      = 'multilineattr';
    const EXTENSION_NAMESPACE = 'ajaxsearch';
    
    const EXTENSION_NAME    = 'Book Information';
    const EXTENSION_VERSION = '1.1.11';
    const EXTENSION_EDITION = 'Basic';
    
    protected $_activeAttr;
    protected $_minChars;
    protected $_maxResault;
    protected $_separator;
    protected $_currentType;
    protected $_addButtonShow;
    protected $_searchType;
    
    public function __construct()
    {
        $this->setSearchMinChars();
        $this->setMaxResults();
        $this->setSeparator();
        $this->setAddButtonShow();
        $this->setSearchType();
    }
    
    public static function getNamespace()
    {
        return self::PATH_NAMESPACE . '/' . self::EXTENSION_NAMESPACE . '/';
    }
    
    public function getExtensionName()
    {
        return self::EXTENSION_NAME;
    }
    
    public function getExtensionVersion()
    {
        return self::EXTENSION_VERSION;
    }
    
    public function getExtensionEdition()
    {
        return self::EXTENSION_EDITION;
    }

    public function isActive()
    {
        return Mage::getStoreConfigFlag(self::getNamespace().'active');
    }

    public function isEnabled()
    {
        return $this->isActive();
    }
    
    public function setActiveAttr(string $attr = null)
    {
        $this->_activeAttr = is_null($attr) 
                ? (string) Mage::getStoreConfig(self::getNamespace().'active_attr')
                : (string) $attr;
        return $this;
    }

    public function getActiveAttr($array = true)
    {
        if (!$this->isActive()) {
            $this->setActiveAttr('none');
        }
        if (is_null($this->_activeAttr)) {
            $this->setActiveAttr();
        }
        $allowedAttrs = explode(',', $this->_activeAttr);
        
        if (!$array) {
            return in_array('none', $allowedAttrs) ? '' : $this->_activeAttr;
        }
        return in_array('none', $allowedAttrs) ? array() : $allowedAttrs;
    }
    
    public function setSearchMinChars(integer $minChars = null)
    {
        $this->_minChars = is_null($minChars) 
                ? (int) Mage::getStoreConfig(self::getNamespace().'minchars')
                : (int) $minChars;
        return $this;
    }

    public function getSearchMinChars()
    {
        if (is_null($this->_minChars)) {
            $this->setSearchMinChars();
        }
        return $this->_minChars;
    }
    
    public function setMaxResults(integer $maxResults = null)
    {
        $this->_maxResault = is_null($maxResults) 
                ? (int) Mage::getStoreConfig(self::getNamespace().'maxresault')
                : (int) $maxResults;
        return $this;
    }

    public function getMaxResults()
    {
        if (is_null($this->_maxResault)) {
            $this->setMaxResults();
        }
        return $this->_maxResault;
    }
    
    public function setSeparator(string $separator = null)
    {
        if (is_null($separator)) {
            $type = Mage::getStoreConfig(self::getNamespace().'separator');
            if ($type == 'new_line') {
                $this->_separator = '<br />';
            }
            else {
                $this->_separator = $this->_getCustomSeparator();
            }
        }
        else {
            $this->_separator = (string) $separator;
        }
        return $this;
    }

    protected function _getCustomSeparator()
    {
        return Mage::getStoreConfig(self::getNamespace().'custom_separator');
    }

    public function getSeparator()
    {
        if (is_null($this->_separator)) {
            $this->setSeparator();
        }
        return $this->_separator;
    }
    
    public function setAddButtonShow(string $type = null)
    {
        $this->_addButtonShow = is_null($type) 
                ? Mage::getStoreConfig(self::getNamespace().'show_add')
                : (string) $type;
        return $this;
    }

    public function addButtonShowType()
    {
        if ($this->searchMainTable()) {
            return false;
        }
        if (is_null($this->_addButtonShow)) {
            $this->setAddButtonShow();
        }
        return $this->_addButtonShow;
    }
    
    public function setSearchType(string $type = null)
    {
        $this->_searchType = is_null($type) 
                ? Mage::getStoreConfig(self::getNamespace().'search_type') 
                : (string) $type;
        return $this;
    }

    public function getSearchType()
    {
        if (is_null($this->_searchType)) {
            $this->setSearchType();
        }
        return $this->_searchType;
    }
    
    public function setShowCurrentType(boolean $current = null)
    {
        $this->_currentType = is_null($current) 
                ? Mage::getStoreConfig(self::getNamespace().'type') 
                : (bool) $current;
        return $this;
    }
    
    public function showCurrentType()
    {
        if (is_null($this->_currentType)) {
            $this->setShowCurrentType();
        }
        return $this->_currentType;
    }
    
    public function searchMainTable()
    {
        return Mage::getStoreConfig(self::getNamespace().'search_table');
    }
    
}
