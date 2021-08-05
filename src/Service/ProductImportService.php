<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ProductData;
use App\Repository\ProductDataRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;

class ProductImportService
{
    /**
     * @var ProductDataRepository
     */
    private $productDataRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    private const CSV_COLUMN_PRODUCT_CODE = 'Product Code';
    private const CSV_COLUMN_PRODUCT_NAME = 'Product Name';
    private const CSV_COLUMN_PRODUCT_DESCRIPTION = 'Product Description';
    private const CSV_COLUMN_STOCK = 'Stock';
    private const CSV_COLUMN_COST = 'Cost in GBP';
    private const CSV_COLUMN_DISCONTINUED = 'Discontinued';

    /**
     * @param ProductDataRepository $productDataRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(ProductDataRepository $productDataRepository, SerializerInterface $serializer)
    {
        $this->productDataRepository = $productDataRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param string|null $filePath
     * @param bool $ignoreFirstLine
     * @return array
     */
    public function importFromCSV(?string $filePath, bool $ignoreFirstLine = true): array
    {
        if (empty($filePath)) {
            throw new \LogicException('Path to CSV file is required and must be valid', 11);
        }
        $filePath = str_replace("\\", '/', $filePath);
        if (!is_file($filePath)) {
            throw new \LogicException('Invalid path to file', 12);
        }
        $data = file_get_contents($filePath);
        if ($data === false) {
            throw new \LogicException('Unable to open the file of "'.$filePath.'" for reading', 14);
        }
        $imported = $this->serializer->decode($data, 'csv');

        return $this->filterImportedProducts($imported);
    }

    /**
     * @param array $data
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function storeToDatabase(array $data): void
    {
        foreach ($data as $row) {
            $productData = new ProductData();
            $productData->setProductCode($row[self::CSV_COLUMN_PRODUCT_CODE]);
            $productData->setProductName($row[self::CSV_COLUMN_PRODUCT_NAME]);
            $productData->setProductDesc($row[self::CSV_COLUMN_PRODUCT_DESCRIPTION]);
            $productData->setStock((int) $row[self::CSV_COLUMN_STOCK]);
            $productData->setCost((float) $row[self::CSV_COLUMN_COST]);
            $productData->setDiscontinued(
                (mb_strtolower(trim($row[self::CSV_COLUMN_DISCONTINUED])) === mb_strtolower('yes')) ?
                    new \DateTime() :
                    null
            );
            $this->productDataRepository->preSave($productData);
        }
        $this->productDataRepository->save();
    }

    /**
     * @param array $data
     * @return array
     */
    private function filterImportedProducts(array $data): array
    {
        $info = [
            'total_rows_qty' => count($data),
            'rows_successfully_imported' => 0,
            'rows_skipped' => 0,
            'skipped_row_numbers' => [],
            'skipped_rows_content' => [],
            'filtered_rows' => [],
        ];
        foreach ($data as $i => $row) {
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
        $info['filtered_rows'] = $this->excludeDuals($info);
        $info['rows_successfully_imported'] = $info['total_rows_qty'] - $info['rows_skipped'];

        return $info;
    }

    /**
     * @param array $info
     * @return array
     */
    private function excludeDuals(array &$info): array
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
