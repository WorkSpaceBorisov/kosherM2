#!/usr/bin/env bash

bold=$(tput bold)
normal=$(tput sgr0)

echo "${bold}Import clean M1 DB:${normal}"
madock cli "pv /var/www/html/cleardbm1.sql | mysql -uroot -ppassword -h db kosher"
echo "${bold}Run prep scripts M1:${normal}"
madock cli "pv app/code/Kosher/Migration/query/cleanup-m1-db-for-dev.sql | mysql -uroot -ppassword -h db kosher"
echo "${bold}Import clean M2 DB:${normal}"
madock cli "pv /var/www/html/cleardbm2.sql | mysql -uroot -ppassword -h db magento"
echo "${bold}Run prep scripts M2:${normal}"
madock cli "pv app/code/Kosher/Migration/query/before_migration_magento2.sql | mysql -uroot -ppassword -h db magento"
echo "${bold}• Clean Temp Dirs ${normal}" && rm -rf ./generated/* ./var/* ./pub/static/* &&
echo "${bold}• Install packages ${normal}" && rm -rf ./vendor ./setup ./bin ./lib ./phpserver ./dev ./.docker && madock composer clear-cache && madock composer install &&
echo "${bold}• Revert etc/di.xml ${normal}" && git checkout -- app/etc/di.xml &&
echo "${bold}• Revert nginx.conf.sample ${normal}" && git checkout -- nginx.conf.sample &&
echo "${bold}Create var/tmp folder:${normal}"
mkdir -p var/tmp
echo "${bold}Setup upgrade:${normal}"
madock m setup:upgrade
echo "${bold}Remove existing lock files:${normal}"
rm var/migration-tool-progress.lock || true
rm var/migration.log || true
echo "${bold}Starting migration:${normal}"
echo "${bold}Migrate settings:${normal}"
madock m migrate:settings app/code/Kosher/Migration/etc/opensource-to-opensource/1.9.4.2/config.xml
echo "${bold}Migrate data:${normal}"
madock m migrate:data app/code/Kosher/Migration/etc/opensource-to-opensource/1.9.4.2/config.xml
echo "${bold}After Migration Script:${normal}"
madock cli "pv app/code/Kosher/Migration/query/after_migration_magento2.sql | mysql -uroot -ppassword -h db magento"
echo "${bold}Setup upgrade:${normal}"
madock m setup:upgrade
echo "${bold}Reindex:${normal}"
madock m indexer:reindex && echo "• done ✔"
