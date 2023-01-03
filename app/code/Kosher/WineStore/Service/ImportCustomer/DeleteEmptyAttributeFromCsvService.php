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

    public function execute(array $data)
    {
        $this->csvData = $data;
//        foreach ($data as $email => $customerData) {
//                foreach ($this->forDelete as $item){
//                    unset($this->csvData[$email][$item]);
//                }
//
//        }
        return $this->csvData;
    }
}
