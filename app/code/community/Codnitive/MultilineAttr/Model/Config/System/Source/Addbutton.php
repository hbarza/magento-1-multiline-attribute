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

class Codnitive_MultilineAttr_Model_Config_System_Source_Addbutton extends Mage_Core_Model_Config_Data
{

    const SHOW_ALWAYS = 'always';
    const NOT_EXISTS  = 'notexists';

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::SHOW_ALWAYS,
                'label' => Mage::helper('multilineattr')->__('Show Always')
            ),
            array(
                'value' => self::NOT_EXISTS,
                'label' => Mage::helper('multilineattr')->__('When Title not Exists')
            ),
        );
    }

}
