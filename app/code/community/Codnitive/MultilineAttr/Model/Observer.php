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

class Codnitive_MultilineAttr_Model_Observer extends Mage_Core_Model_Abstract
{
    
    public function setMultilineRendererInForm(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();

        $attributes = Mage::getSingleton('multilineattr/multiline')->getMultilineAttributeCodes(true);
        foreach ($attributes as $code) {
            if ($multiline = $form->getElement($code)) {
                $multiline->setRenderer(
                    Mage::app()->getLayout()->createBlock('multilineattr/renderer_multilineattr_multiline')
                );
            }
        }

        return $this;
    }

    public function updateExcludedFieldList(Varien_Event_Observer $observer)
    {
        $block      = $observer->getEvent()->getObject();
        $list       = $block->getFormExcludedFieldList();
        $attributes = Mage::getSingleton('multilineattr/multiline')->getMultilineAttributeCodes(true);
        $list       = array_merge($list, array_values($attributes));

        $block->setFormExcludedFieldList($list);

        return $this;
    }

    public function addMultilineAttributeType(Varien_Event_Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        
        $types[] = array(
            'value' => 'multiline',
            'label' => Mage::helper('multilineattr')->__('Multiline'),
            'hide_fields' => array(
                'is_unique',
                'is_required',
                'frontend_class',
                'is_configurable',
                '_scope',
                '_default_value',
                '_front_fieldset',
            ),
        );
        $response->setTypes($types);

        return $this;
    }

    public function assignBackendModelToAttribute(Varien_Event_Observer $observer)
    {
        $backendModel  = Codnitive_MultilineAttr_Model_Attribute_Backend_Multilineattr_Multiline::getBackendModelName();
        $frontendModel = Codnitive_MultilineAttr_Model_Entity_Attribute_Frontend_Multiline::getFrontendModelName();
        
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'multiline') {
            $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
            $object->setEntityTypeId($entityTypeID);
            $object->setBackendModel($backendModel);
            $object->setFrontendModel($frontendModel);
            $object->setFrontendInput('multiline');
            $object->setIsRequired('1');
            $object->setIsUserDefined('0');
            $object->setDefaultValue(null);
            $object->setIsUnique('0');
            $object->setIsGlobal(1);
            $object->setIsVisible('1');
            $object->setIsSearchable('0');
            $object->setIsComparable('0');
            $object->setIsVisibleOnFront('1');
            $object->setIsHtmlAllowedOnFront('1');
            $object->setIsUsedForPriceRules('0');
            $object->setIsFilterableInSearch('0');
            $object->setUsedInProductListing('0');
            $object->setUsedForSortBy('0');
            $object->setIsConfigurable('0');
            $object->setIsVisibleInAdvancedSearch('1');
            $object->setIsUsedForPromoRules('0');
            if (!$object->getApplyTo()) {
                $applyTo = array();
                foreach (Mage_Catalog_Model_Product_Type::getOptions() as $option) {
                    $applyTo[] = $option['value'];
                }
                $object->setApplyTo($applyTo);
            }
            $this->_addTaxtBaseArea($observer);
        }

        return $this;
    }

    public function updateElementTypes(Varien_Event_Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        $types    = $response->getTypes();
        $types['multiline'] = Mage::getConfig()->getBlockClassName('multilineattr/element_multilineattr_multiline');
        $response->setTypes($types);
        return $this;
    }
    
    public function updateListTextBaseValue(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();
        $separator = Mage::getModel('multilineattr/config')->getSeparator();
        
        foreach ($product->getAttributes() as $attribute) {
            if ($attribute->getFrontendInput() == 'multiline') {
                $code = $attribute->getAttributeCode();
                $className = uc_words($code, '', '_');
                $getter = 'get' . $className;
                $titles = $product->$getter();
                $textBaseValue = '';
                
                if ($titles) {
                    foreach ($titles as $title) {
                        if ((isset($title['delete']) && $title['delete']) || !isset($title['line_title'])) {
                            continue;
                        }
                        $textBaseValue .= $title['line_title'] . $separator;
                    }
                }
                
                $textBase = preg_replace('/'. preg_quote($separator, '/') . '$/', '', $textBaseValue);
                $setter = 'set' . $className . 'TextBase';
                $product->$setter($textBase);
            }
        }
        
        return $this;
    }
    
    protected function _addTaxtBaseArea($observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        $data = array(
            'is_global'                     => $attribute->getIsGlobal(),
            'frontend_input'                => 'textarea',
            'default_value_text'            => '',
            'default_value_yesno'           => '0',
            'default_value_date'            => '',
            'default_value_textarea'        => '',
            'is_unique'                     => '0',
            'is_required'                   => '0',
            'frontend_class'                => '',
            'is_searchable'                 => '1',
            'is_visible_in_advanced_search' => '0',
            'is_comparable'                 => '1',
            'is_used_for_promo_rules'       => '0',
            'is_html_allowed_on_front'      => '1',
            'is_visible_on_front'           => '0',
            'used_in_product_listing'       => '0',
            'used_for_sort_by'              => '0',
            'is_configurable'               => '0',
            'is_filterable'                 => '0',
            'is_filterable_in_search'       => '0',
            'backend_type'                  => 'text',
            'default_value'                 => '',
            'source_model'                  => '',
            'backend_model'                 => '',
            'frontend_label'                => $attribute->getFrontendLabel(),
            'apply_to'                      => $attribute->getApplyTo(),
        );
            
        $data['attribute_code'] = $attribute->getAttributeCode().'_text_base';

        $model = Mage::getModel('catalog/resource_eav_attribute');
        $model->addData($data);

        $attributeSetId = $attribute->getAttributeSetId() ? $attribute->getAttributeSetId() : 4;
        $attributeGroupId = $attribute->getAttributeGroupId() ? $attribute->getAttributeGroupId() : 7;
        $model->setAttributeSetId($attributeSetId);
        $model->setAttributeGroupId($attributeGroupId);

        $model->setEntityTypeId($attribute->getEntityTypeId());

        $model->setIsUserDefined(0);
        $model->setIsVisible(0);
            
        try {
            $model->save();
        }
        catch(Exception $e) {
            $this->logError('Attribute ['.$data['attribute_code'].'] could not be saved: ' . $e->getMessage());
            return false;
        }
    }

}

