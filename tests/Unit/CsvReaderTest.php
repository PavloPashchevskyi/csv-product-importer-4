<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\ProductDTO;
use App\Service\Interfaces\ReaderInterface;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    public function testRead()
    {
        $csvReader = $this->createMock(ReaderInterface::class);
        $productDTO = $this->createMock(ProductDTO::class);

        $productDTO->setCode('P0002');
        $productDTO->setName('Cd Player');
        $productDTO->setDescription('Nice CD player');
        $productDTO->setStock(11);
        $productDTO->setCost('50.12');
        $productDTO->setDiscontinued('yes');

        $csvReader
            ->expects($this->once())
            ->method('read')
            ->with('/some/path/to/csv/file.csv')
            ->willReturn([$productDTO]);

        $DTOs = $csvReader->read('/some/path/to/csv/file.csv');

        $this->assertCount(1, $DTOs);
        $this->assertInstanceOf(ProductDTO::class, $DTOs[0]);
    }
}
