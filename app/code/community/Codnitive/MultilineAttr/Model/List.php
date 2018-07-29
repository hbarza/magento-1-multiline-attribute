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

class Codnitive_MultilineAttr_Model_List extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('multilineattr/list');
    }
    
    public function getAttributeCode($id)
    {
        $attribute = $this->getAttribute($id);
        if ($attribute) {
            return $attribute->getAttributeCode();
        }
        
        return false;
    }
    
    public function getAttributeId($code)
    {
        $attribute = $this->getAttribute($code);
        if ($attribute) {
            return $attribute->getId();
        }
        
        return false;
    }

    public function getAttribute($attribute)
    {
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $model = Mage::getResourceModel('catalog/eav_attribute')
            ->setEntityTypeId($entityTypeId);

        if (is_numeric($attribute)) {
            $model->load(intval($attribute));
        } else {
            $model->load($attribute, 'attribute_code');
        }

        if (!$model->getId()) {
            return false;
        }

        return $model;
    }

}