<?php

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
