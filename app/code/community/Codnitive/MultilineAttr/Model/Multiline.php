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

class Codnitive_MultilineAttr_Model_Multiline extends Mage_Core_Model_Abstract
{

    protected $_allAttributes = null;

    protected function _construct()
    {
        $this->_init('multilineattr/multiline', 'multilineattr/multiline');
    }

    public function getMultilineAttributeCodes($forceEnabled = false)
    {
        return $this->getMultilineAttrAttributeCodes($forceEnabled);
    }

    public function getMultilineAttrAttributeCodes($forceEnabled = false)
    {
        if (!$forceEnabled && !Mage::helper('multilineattr')->isEnabled()) {
            return array();
        }

        if (is_null($this->_allAttributes)) {
            $this->_allAttributes = Mage::getModel('eav/entity_attribute')->getAttributeCodesByFrontendType('multiline');
        }
        return $this->_allAttributes;
    }

}
