<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDTO;
use App\Handler\ProductDataHandler;
use App\Service\Interfaces\ProductImportInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class DatabaseSavingService implements ProductImportInterface
{
    /**
     * @var ProductDataHandler
     */
    private $productDataHandler;

    /**
     * @param ProductDataHandler $productDataHandler
     */
    public function __construct(ProductDataHandler $productDataHandler)
    {
        $this->productDataHandler = $productDataHandler;
    }

    /**
     * @param array $data
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(array $data): void
    {
        $i = 0;
        foreach ($data as $row) {
            $productDTO = new ProductDTO($row[self::CSV_COLUMN_PRODUCT_CODE], $row[self::CSV_COLUMN_PRODUCT_NAME], $row[self::CSV_COLUMN_PRODUCT_DESCRIPTION]);
            $productDTO->setStock((int) $row[self::CSV_COLUMN_STOCK]);
            $productDTO->setCost($row[self::CSV_COLUMN_COST]);
            $productDTO->setDiscontinued($row[self::CSV_COLUMN_DISCONTINUED]);
            $this->productDataHandler->handle($productDTO, $i === count($data) - 1);
            $i++;
        }
    }
}
