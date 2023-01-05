<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service\ImportCustomer;

class DeleteEmptyAttributeFromCsvService
{
    private array $forDelete =
        [
            "is_builder_account",
            "mgs_social_fid",
            "mgs_social_ftoken",
            "mgs_social_gid",
            "mgs_social_gtoken",
            "mgs_social_tid",
            "mgs_social_ttoken",
            "password_salt",
            "pwd_validate_method",
            "telephone",
            "url",
            "newsletter",
            "fax"
        ];

    private array $csvData = [];

    /**
     * @param array $data
     * @return array
     */
    public function execute(array $data): array
    {
        $this->csvData = $data;
        foreach ($data as $email => $customerData) {
            if ($email != 'header') {
                $this->csvData[$email]['_store'] = 'default';
            }

            foreach ($this->forDelete as $item) {
                if ($email != 'header') {
                    unset($this->csvData[$email][$item]);
                }

                if ($email == 'header') {
                    $headerKey = array_search($item, $this->csvData[$email]);
                    unset($this->csvData[$email][$headerKey]);
                }
            }
        }

        return $this->csvData;
    }
}
