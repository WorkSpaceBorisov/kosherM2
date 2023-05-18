#!/usr/bin/env bash

bold=$(tput bold)
normal=$(tput sgr0)

echo "${bold}Setup upgrade:${normal}"
madock m setup:upgrade
echo "${bold}Clean Url Rewrite Entity${normal}"
madock cli "pv app/code/Kosher/WineStore/MigrationQuery/after_migration_wine_product.sql | mysql -uroot -ppassword -h db magento"
echo "${bold}Regenerate Catalog Url Entity${normal}"
madock m ok:urlrewrites:regenerate --entity-type=category
echo "${bold}Regenerate Product Url Entity${normal}"
madock m ok:urlrewrites:regenerate --entity-type=product
madock m indexer:reindex  && echo "• done ✔"
