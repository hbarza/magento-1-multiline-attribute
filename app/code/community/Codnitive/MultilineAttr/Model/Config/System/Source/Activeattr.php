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

class Codnitive_MultilineAttr_Model_Config_System_Source_Activeattr extends Mage_Core_Model_Config_Data
{

    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => 'none', 
                'label' => Mage::helper('multilineattr')->__('None')
            )
        );
        
        $collection = Mage::getResourceModel('eav/entity_attribute_collection');
        $collection->setEntityTypeFilter(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId())->load();
        $attributes = $collection->getItems();
        
        foreach ($attributes as $attribute) {
            if ($attribute->getFrontendInput() == 'multiline') {
                $options[] = array(
                    'value' => $attribute->getAttributeCode(), 
                    'label' => Mage::helper('multilineattr')->__($attribute->getFrontendLabel())
                );
            }
        }
        
        return $options;
    }

}
