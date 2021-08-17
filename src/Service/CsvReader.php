<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDTO;
use App\Service\Interfaces\ReaderInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
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
     * @var FileContentGetter
     */
    private $fileContentGetter;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @param FileContentGetter $fileContentGetter
     * @param DecoderInterface $decoder
     */
    public function __construct(FileContentGetter $fileContentGetter, DecoderInterface $decoder)
    {
        $this->fileContentGetter = $fileContentGetter;
        $this->decoder = $decoder;
    }

    /**
     * @param string|null $filePath
     * @return array
     */
    public function read(?string $filePath): array
    {
        $data = $this->fileContentGetter->getContent($filePath);
        $imported = $this->decoder->decode($data, 'csv');

        $DTOs = [];
        foreach ($imported as $row) {
            $productDTO = new ProductDTO();
            $productDTO->setCode($row[self::CSV_COLUMN_PRODUCT_CODE] ?? null);
            $productDTO->setName($row[self::CSV_COLUMN_PRODUCT_NAME] ?? null);
            $productDTO->setDescription($row[self::CSV_COLUMN_PRODUCT_DESCRIPTION] ?? null);
            $productDTO->setCost($row[self::CSV_COLUMN_COST] ?? null);
            $productDTO->setStock((int) $row[self::CSV_COLUMN_STOCK] ?? null);
            $productDTO->setDiscontinued($row[self::CSV_COLUMN_DISCONTINUED] ?? null);
            $DTOs[] = $productDTO;
        }

        return $DTOs;
    }
}