#!/usr/bin/env bash

bold=$(tput bold)
normal=$(tput sgr0)

echo "${bold}Clean Url Rewrite Entity${normal}"
pv app/code/Kosher/WineStore/MigrationQuery/after_migration_wine_product.sql | mysql -um2_kosher4u_eu -pIN2uXFYHM1U10VLF m2_kosher4u_eu
echo "${bold}Regenerate Catalog Url Entity${normal}"
bin/magento ok:urlrewrites:regenerate --entity-type=category
echo "${bold}Regenerate Product Url Entity${normal}"
bin/magento ok:urlrewrites:regenerate --entity-type=product
php bin/magento indexer:reindex  && echo "• done ✔"
