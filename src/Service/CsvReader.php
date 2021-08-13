<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDTO;
use App\Service\Interfaces\ReaderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CsvReader implements ReaderInterface
{
    private const CSV_COLUMN_PRODUCT_CODE = 'Product Code';
    private const CSV_COLUMN_PRODUCT_NAME = 'Product Name';
    private const CSV_COLUMN_PRODUCT_DESCRIPTION = 'Product Description';
    private const CSV_COLUMN_STOCK = 'Stock';
    private const CSV_COLUMN_COST = 'Cost in GBP';
    private const CSV_COLUMN_DISCONTINUED = 'Discontinued';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string|null $filePath
     * @return array
     */
    public function read(?string $filePath): array
    {
        if (empty($filePath)) {
            throw new \LogicException('Path to CSV file is required and must be valid');
        }

        $filePath = str_replace("\\", '/', $filePath);

        if (!is_file($filePath)) {
            throw new \LogicException('Invalid path to file');
        }

        $data = file_get_contents($filePath);

        if ($data === false) {
            throw new \LogicException('Unable to open the file of "'.$filePath.'" for reading');
        }

        $imported = $this->serializer->decode($data, 'csv');

        $DTOs = [];
        foreach ($imported as $row) {
            $productDTO = new ProductDTO($row[self::CSV_COLUMN_PRODUCT_CODE], $row[self::CSV_COLUMN_PRODUCT_NAME], $row[self::CSV_COLUMN_PRODUCT_DESCRIPTION]);
            $productDTO->setCost($row[self::CSV_COLUMN_COST]);
            $productDTO->setStock((int) $row[self::CSV_COLUMN_STOCK]);
            $productDTO->setDiscontinued($row[self::CSV_COLUMN_DISCONTINUED]);
            $DTOs[] = $productDTO;
        }

        return $DTOs;
    }
}