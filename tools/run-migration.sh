#!/usr/bin/env bash

bold=$(tput bold)
normal=$(tput sgr0)

echo "${bold}Import clean M1 DB:${normal}"
pv /home/radion/Документы/Sites/kosherm2dump/cleardbm1.sql | mysql -um2_kosher4u_eu -pIN2uXFYHM1U10VLF kosher
echo "${bold}Run prep scripts M1:${normal}"
pv app/code/Kosher/Migration/query/cleanup-m1-db-for-dev.sql | mysql -um2_kosher4u_eu -pIN2uXFYHM1U10VLF kosher
echo "${bold}Import clean M2 DB:${normal}"
pv /home/radion/Документы/Sites/kosherm2dump/cleardbm2.sql | mysql -um2_kosher4u_eu -pIN2uXFYHM1U10VLF m2_kosher4u_eu
echo "${bold}• Clean Temp Dirs ${normal}" && rm -rf ./generated/* ./var/* ./pub/static/* &&
echo "${bold}• Install packages ${normal}" && rm -rf ./vendor ./setup ./bin ./lib ./phpserver ./dev ./.docker && composer clear-cache && composer install &&
echo "${bold}• Revert etc/di.xml ${normal}" && git checkout -- app/etc/di.xml &&
echo "${bold}• Revert nginx.conf.sample ${normal}" && git checkout -- nginx.conf.sample &&
echo "${bold}Remove existing lock files:${normal}"
rm var/migration-tool-progress.lock || true
rm var/migration.log || true
echo "${bold}Starting migration:${normal}"
echo "${bold}Migrate settings:${normal}"
bin/magento migrate:settings app/code/Kosher/Migration/etc/opensource-to-opensource/1.9.4.2/config.xml
echo "${bold}Migrate data:${normal}"
bin/magento migrate:data app/code/Kosher/Migration/etc/opensource-to-opensource/1.9.4.2/config.xml
echo "${bold}After Migration Script:${normal}"
pv app/code/Kosher/Migration/query/after_migration_magento2.sql | mysql -um2_kosher4u_eu -pIN2uXFYHM1U10VLF m2_kosher4u_eu
echo "${bold}Setup upgrade:${normal}"
php bin/magento setup:upgrade
echo "${bold}Reindex:${normal}"
php bin/magento indexer:reindex  && echo "• done ✔"
