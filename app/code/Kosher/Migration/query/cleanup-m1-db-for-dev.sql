delete from catalog_eav_attribute where attribute_id = 121;
delete from catalog_product_entity_int where attribute_id = 121;
delete from eav_entity_attribute where attribute_id = 121;

delete from catalog_product_entity where sku is NULL;
