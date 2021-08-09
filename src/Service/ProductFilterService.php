<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Interfaces\ProductImportInterface;

class ProductFilterService implements ProductImportInterface
{
    /**
     * @param string|null $filePath
     */
    public function validateFilepath(?string $filePath): void
    {
        if (empty($filePath)) {
            throw new \LogicException('Path to CSV file is required and must be valid', 11);
        }
        $filePath = str_replace("\\", '/', $filePath);
        if (!is_file($filePath)) {
            throw new \LogicException('Invalid path to file', 12);
        }
    }

    /**
     * @param array $info
     * @return array
     */
    public function excludeDuals(array &$info): array
    {
        $data = $info['filtered_rows'];
        $info['dual_row_numbers'] = [];
        $dataWithoutDuals = [];
        $dataCnt = count($data);
        for ($i = 0; $i < $dataCnt; $i++) {
            for ($j = $i + 1; $j < $dataCnt; $j++) {
                if ($data[$i][self::CSV_COLUMN_PRODUCT_CODE] === $data[$j][self::CSV_COLUMN_PRODUCT_CODE]) {
                    $info['dual_row_numbers'][] = $j;
                    $info['skipped_row_numbers'][] = $j;
                    $info['skipped_rows_content'][] = $data[$j];
                    $info['rows_skipped']++;
                }
            }
            if (!in_array($i, $info['dual_row_numbers'])) {
                $dataWithoutDuals[] = $data[$i];
            }
        }
        return $dataWithoutDuals;
    }
}