<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDTO;

class ProductInfo
{
    private int $totalRowsQuantity = 0;

    private int $skippedRowsQuantity = 0;

    private array $skippedRowsContent = [];

    private array $filteredRowsContent = [];

    private array $dualRowsNumbers = [];

    /**
     * @return int
     */
    public function getTotalRowsQuantity(): int
    {
        return $this->totalRowsQuantity;
    }

    /**
     * @param int $totalRowsQuantity
     */
    public function setTotalRowsQuantity(int $totalRowsQuantity): void
    {
        $this->totalRowsQuantity = $totalRowsQuantity;
    }

    /**
     * @return int
     */
    public function getRowsSuccessfullyImported(): int
    {
        return $this->totalRowsQuantity - $this->skippedRowsQuantity;
    }

    /**
     * @return int
     */
    public function getSkippedRowsQuantity(): int
    {
        return $this->skippedRowsQuantity;
    }

    /**
     * @return array
     */
    public function getSkippedRowsAsArray(): array
    {
        $skippedRows = [];
        /** @var ProductDTO $skippedRow */
        foreach ($this->skippedRowsContent as $skippedRow) {
            $skippedRows[] = [
                $skippedRow->getCode(), $skippedRow->getName(), $skippedRow->getDescription(), $skippedRow->getStock(), $skippedRow->getCost(), $skippedRow->getDiscontinued(),
            ];
        }

        return $skippedRows;
    }

    /**
     * @param ProductDTO $skippedRow
     */
    public function addSkippedRow(ProductDTO $skippedRow): void
    {
        $this->skippedRowsContent[] = $skippedRow;
        $this->skippedRowsQuantity++;
    }

    /**
     * @return ProductDTO[]
     */
    public function getFilteredRowsContent(): array
    {
        return $this->filteredRowsContent;
    }

    /**
     * @param ProductDTO[] $filteredRowsContent
     */
    public function setFilteredRowsContent(array $filteredRowsContent = []): void
    {
        $this->filteredRowsContent = $filteredRowsContent;
    }

    /**
     * @param ProductDTO $filteredRow
     */
    public function addFilteredRow(ProductDTO $filteredRow): void
    {
        $this->filteredRowsContent[] = $filteredRow;
    }

    /**
     * @return int[]
     */
    public function getDualRowsNumbers(): array
    {
        return $this->dualRowsNumbers;
    }

    /**
     * @param int $dualRowNumber
     */
    public function addDualRowNumber(int $dualRowNumber)
    {
        $this->dualRowsNumbers[] = $dualRowNumber;
    }
}
