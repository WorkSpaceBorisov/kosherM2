UPDATE core_config_data set value = 0 where path = 'catalog/frontend/list_allow_all'
update core_config_data set value = 'https://pesach.m2.kosher4u.eu pesach/' where path = "web/unsecure/base_url";
update core_config_data set value = 'https://pesach.m2.kosher4u.eu pesach/' where path = "web/unsecure/base_link_url";
update core_config_data set value = 'https://pesach.m2.kosher4u.eu pesach/' where path = "web/secure/base_url";
update core_config_data set value = 'https://pesach.m2.kosher4u.eu pesach/' where path = "web/secure/base_link_url";
