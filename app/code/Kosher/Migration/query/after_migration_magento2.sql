UPDATE core_config_data set value = 0 where path = 'catalog/frontend/list_allow_all';

UPDATE store_group set name = 'KOSHER4U' where name = 'Main Website Store';
UPDATE store_website set name = 'KOSHER4U' where name = 'Main Website';

DELETE FROM `patch_list` where patch_name = 'Kosher\\WineStore\\Setup\\Patch\\Data\\CreateWineStoreDataPatch';
DELETE FROM `patch_list` where patch_name = 'Kosher\\StoresConfiguration\\Setup\\Patch\\Data\\RemoveMailchimpAttributesDataPatch';
DELETE FROM `patch_list` where patch_name = 'Kosher\\CategoryAdjustment\\Setup\\Patch\\Data\\SetImagePathToCategoryDataPatch';

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

insert into `core_config_data` (`config_id`, `scope`, `scope_id`, `path`, `value`, `updated_at`)
VALUES (NULL, 'default', 0, 'aitoie/general/bulk_count', '10000', current_timestamp());

insert into `core_config_data`
SET config_id  = NULL,
    scope      = "websites",
    scope_id   = (select store_id from store where code = "default"),
    path       = "design/theme/theme_id",
    value      = (select theme_id from theme where code = "Kosher/default"),
    updated_at = current_timestamp();

update design_config_grid_flat
set theme_theme_id = (select theme_id
                      from theme
                      where code = "Kosher/default")
where store_website_id = (select website_id
                          from store_website
                          where code = "base");
UPDATE `mst_credit_balance` SET `created_at`= current_timestamp(), `updated_at`= current_timestamp();
UPDATE `mst_credit_balance` SET `currency_code`= 'EUR';
