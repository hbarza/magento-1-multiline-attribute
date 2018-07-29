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

class Codnitive_MultilineAttr_Model_Entity_Attribute_Frontend_Multiline extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    public static function getFrontendModelName()
    {
        return 'multilineattr/entity_attribute_frontend_multiline';
    }
    
    public function getValue(Varien_Object $object)
    {
        $res = '';
        $value = parent::getValue($object);
        $separator = Mage::getModel('multilineattr/config')->getSeparator();
        
        if ($value) {
            try {
                foreach ($value as $i => $row) {
                    $res .= $value[$i]['website_value'] . $separator;
                }
            } catch (Exception $e) {
                foreach ($value as $i => $row) {
                    $res .= $value[$i]['value'] . $separator;
                }
            }
        }
        return preg_replace('/'. preg_quote($separator, '/') . '$/', '', $res);
    }
    
}

