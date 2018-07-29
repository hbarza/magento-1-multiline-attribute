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

class Codnitive_MultilineAttr_Model_Attribute_Backend_Multilineattr_Multiline extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public static function getBackendModelName()
    {
        return 'multilineattr/attribute_backend_multilineattr_multiline';
    }
    
    protected function _getResource()
    {
        return Mage::getResourceSingleton(self::getBackendModelName());
    }

    public function validate($object)
    {
        $lines = $object->getData($this->getAttribute()->getName());
        if (empty($lines)) {
            return $this;
        }
        $dup = array();

        foreach ($lines as $line) {
            if (!empty($line['delete'])) {
                continue;
            }

            $key1 = $line['line_title'];

            if (!empty($dup[$key1])) {
                Mage::throwException(
                    Mage::helper('catalog')->__("Duplicate Title '%s' found.", $key1)
                );
            }
            $dup[$key1] = 1;
        }
        return $this;
    }

    public function afterLoad($object)
    {
        $data = $this->_getResource()->loadProductData($object, $this->getAttribute());

        foreach ($data as $i => $row) {
            if ($data[$i]['website_id'] == 0) {
                $rate = Mage::app()->getStore()->getBaseCurrency()->getRate(Mage::app()->getBaseCurrencyCode());
                if ($rate) {
                    $data[$i]['website_value'] = $data[$i]['value'];
                } else {
                    unset($data[$i]);
                }
            }
            else {
                $data[$i]['website_value'] = $data[$i]['value'];
            }
        }
        
        $object->setData($this->getAttribute()->getName(), $data);
        return $this;
    }

    public function afterSave($object)
    {
        $orig = $object->getOrigData($this->getAttribute()->getName());
        $current = $object->getData($this->getAttribute()->getName());
        if ($orig == $current) {
            return $this;
        }

        $this->_getResource()->deleteProductData($object, $this->getAttribute());
        $lines = $object->getData($this->getAttribute()->getName());

        if (!is_array($lines)) {
            return $this;
        }

        foreach ($lines as $line) {
            if (empty($line['line_title']) || !empty($line['delete'])) {
                continue;
            }

            $data = array();
            $data['website_id']   = $line['website_id'];
            $data['value']        = $line['line_title'];
            $data['attribute_id'] = $this->getAttribute()->getId();

            $this->_getResource()->insertProductData($object, $data);
        }

        return $this;
    }

    public function afterDelete($object)
    {
        $this->_getResource()->deleteProductData($object, $this->getAttribute());
        return $this;
    }

    public function getTable()
    {
        return $this->_getResource()->getTable('multilineattr/multiline');
    }
    
}

