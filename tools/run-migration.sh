#!/usr/bin/env bash

bold=$(tput bold)
normal=$(tput sgr0)

#echo "${bold}Run prep scripts M1:${normal}"
#mysql -pmagento -umagento kosherm2 app/code/Kosher/Migration/query/cleanup-m1-db-for-dev.sql | mysql -pmagento -umagento kosherm2
echo "${bold}Setup upgrade:${normal}"
bin/magento setup:upgrade
#echo "${bold}Di compile:${normal}"
#bin/magento setup:di:compile
echo "${bold}Remove existing lock files:${normal}"
rm var/migration-tool-progress.lock || true
rm var/migration.log || true
echo "${bold}Starting migration:${normal}"
echo "${bold}Migrate settings:${normal}"
bin/magento migrate:settings app/code/Kosher/Migration/etc/opensource-to-opensource/1.9.4.2/config.xml
echo "${bold}Migrate data:${normal}"
bin/magento migrate:data app/code/Kosher/Migration/etc/opensource-to-opensource/1.9.4.2/config.xml
echo "${bold}Setup upgrade:${normal}"
php bin/magento setup:upgrade
echo "${bold}Reindex:${normal}"
php bin/magento indexer:reindex  && echo "• done ✔"
