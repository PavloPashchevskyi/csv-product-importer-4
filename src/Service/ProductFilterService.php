<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDTO;

class ProductFilterService
{
    /**
     * @param ProductDTO[] $DTOs
     *
     * @return ProductInfo
     */
    public function filter(array $DTOs): ProductInfo
    {
        $productInfo = new ProductInfo();
        $productInfo->setTotalRowsQuantity(count($DTOs));

        foreach ($DTOs as $productDTO) {
            if (
                ($productDTO->getCost() < 5 && $productDTO->getStock() < 10) ||
                ($productDTO->getCost() > 1000)
            ) {
                $productInfo->addSkippedRow($productDTO);
                $productInfo->increaseSkippedRowsQuantity();
            } else {
                $productInfo->addFilteredRow($productDTO);
            }
        }
        $this->excludeDuals($productInfo);

        return $productInfo;
    }

    /**
     * @param ProductInfo $productInfo
     */
    private function excludeDuals(ProductInfo $productInfo): void
    {
        $data = $productInfo->getFilteredRowsContent();
        $dataCnt = count($data);
        $filteredRows = [];

        for ($i = 0; $i < $dataCnt; $i++) {
            for ($j = $i + 1; $j < $dataCnt; $j++) {
                if ($data[$i]->getCode() === $data[$j]->getCode()) {
                    $productInfo->addDualRowNumber($j);
                    $productInfo->addSkippedRow($data[$j]);
                    $productInfo->increaseSkippedRowsQuantity();
                }
            }

            if (!in_array($i, $productInfo->getDualRowsNumbers())) {
                $filteredRows[] = $data[$i];
            }
        }

        $productInfo->setFilteredRowsContent($filteredRows);
    }
}
