UPDATE core_config_data set value = 0 where path = 'catalog/frontend/list_allow_all';

DELETE FROM `patch_list` where patch_name = 'Kosher\\WineStore\\Setup\\Patch\\Data\\CreateWineStoreDataPatch';
DELETE FROM `patch_list` where patch_name = 'Kosher\\StoresConfiguration\\Setup\\Patch\\Data\\RemoveMailchimpAttributesDataPatch';

INSERT INTO `core_config_data` (`config_id`, `scope`, `scope_id`, `path`, `value`, `updated_at`)
VALUES (NULL, 'websites', 2, 'web/unsecure/base_url', 'https://pesach.m2.kosher4u.eu/', current_timestamp());
INSERT INTO `core_config_data` (`config_id`, `scope`, `scope_id`, `path`, `value`, `updated_at`)
VALUES (NULL, 'websites', 2, 'web/unsecure/base_link_url', 'https://pesach.m2.kosher4u.eu/', current_timestamp());
INSERT INTO `core_config_data` (`config_id`, `scope`, `scope_id`, `path`, `value`, `updated_at`)
VALUES (NULL, 'websites', 2, 'web/secure/base_url', 'https://pesach.m2.kosher4u.eu/', current_timestamp());
INSERT INTO `core_config_data` (`config_id`, `scope`, `scope_id`, `path`, `value`, `updated_at`)
VALUES (NULL, 'websites', 2, 'web/secure/base_link_url', 'https://pesach.m2.kosher4u.eu/', current_timestamp());

delete from core_config_data where path = 'design/watermark/image_image' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/image_image' AND scope = 'stores' AND scope_id = 1;
delete from core_config_data where path = 'design/watermark/small_image_image' AND scope = 'stores' AND scope_id = 1;
delete from core_config_data where path = 'design/watermark/thumbnail_image' AND scope = 'stores' AND scope_id = 1;
delete from core_config_data where path = 'design/watermark/image_imageOpacity' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/image_position' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/small_image_image' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/small_image_imageOpacity' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/small_image_position' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/small_image_size' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/thumbnail_image' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/thumbnail_imageOpacity' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/image_image' AND scope = 'default' AND scope_id = 0;
delete from core_config_data where path = 'design/watermark/image_size' AND scope = 'default' AND scope_id = 0;
