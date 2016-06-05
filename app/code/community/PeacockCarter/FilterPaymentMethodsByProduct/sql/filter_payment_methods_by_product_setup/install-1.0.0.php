<?php

/**
 * PeacockCarter
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    PeacockCarter
 * @package     FilterPaymentMethodsByProduct
 * @copyright  Copyright (c) 2016 PeacockCarter Ltd
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$productTypes = array(
    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
);
$productTypes = join(',', $productTypes);

$groupName = 'Payment Methods';

// create Payment Methods attribute group for each attribute set
$entityTypeId           = Mage::getModel('catalog/product')->getResource()->getTypeId();
$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityTypeId);

foreach ($attributeSetCollection as $attributeSet) {
    $installer->addAttributeGroup($entityTypeId, $attributeSet->getAttributeSetId(), $groupName, 2);
}

// create attributes and add to Payment methods group
$installer->addAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'pc_restrict_payment_methods',
    array(
        'group'                   => $groupName,
        'backend'                 => '',
        'frontend'                => '',
        'label'                   => 'Restrict Payment Methods',
        'input'                   => 'select',
        'source'                  => 'eav/entity_attribute_source_boolean',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => false,
        'searchable'              => false,
        'filterable'              => false,
        'comparable'              => false,
        'unique'                  => false,
        'default'                 => '0',
        'apply_to'                => $productTypes,
        'visible_on_front'        => false,
        'used_in_product_listing' => false,
        'is_configurable'         => false,
    )
);

$installer->addAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'pc_restricted_payment_methods',
    array(
        'group'                   => $groupName,
        'type'                    => 'text',
        'backend'                 => 'eav/entity_attribute_backend_array',
        'frontend'                => '',
        'label'                   => 'Restrict All Selected Methods',
        'input'                   => 'multiselect',
        'source'                  => 'peacockcarter_filterpaymentmethodsbyproduct/attribute_source_paymentmethods',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => false,
        'default'                 => '',
        'apply_to'                => $productTypes,
        'visible_on_front'        => false,
        'used_in_product_listing' => false,
        'is_configurable'         => false,
    )
);
