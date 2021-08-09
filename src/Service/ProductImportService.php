<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Interfaces\ProductImportInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProductImportService implements ProductImportInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ProductFilterService
     */
    private $productFilterService;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer, ProductFilterService $productFilterService)
    {
        $this->serializer = $serializer;
        $this->productFilterService = $productFilterService;
    }

    /**
     * @param string|null $filePath
     * @return array
     */
    public function importFromCSV(?string $filePath): array
    {
        $this->productFilterService->validateFilepath($filePath);
        $data = file_get_contents($filePath);
        if ($data === false) {
            throw new \LogicException('Unable to open the file of "'.$filePath.'" for reading', 14);
        }
        $imported = $this->serializer->decode($data, 'csv');
        $info = [
            'total_rows_qty' => count($imported),
            'rows_successfully_imported' => 0,
            'rows_skipped' => 0,
            'skipped_row_numbers' => [],
            'skipped_rows_content' => [],
            'filtered_rows' => [],
        ];
        foreach ($imported as $i => $row) {
            if (
                ($row[self::CSV_COLUMN_COST] < 5 && $row[self::CSV_COLUMN_STOCK] < 10) ||
                ($row[self::CSV_COLUMN_COST] > 1000) ||
                (count($row) > 6)
            ) {
                $info['skipped_row_numbers'][] = $i;
                $info['skipped_rows_content'][] = $row;
                $info['rows_skipped']++;
            } else {
                $info['filtered_rows'][] = $row;
            }
        }
        $info['filtered_rows'] = $this->productFilterService->excludeDuals($info);
        $info['rows_successfully_imported'] = $info['total_rows_qty'] - $info['rows_skipped'];

        return $info;
    }
}
