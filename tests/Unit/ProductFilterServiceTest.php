<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\ProductFilterService;
use PHPUnit\Framework\TestCase;
use App\DTO\ProductDTO;

class ProductFilterServiceTest extends TestCase
{
    public function testFilter()
    {
        $productDTO1 = $this->createMock(ProductDTO::class);
        $productDTO1->setCode('P0002');
        $productDTO1->setName('Cd Player');
        $productDTO1->setDescription('Nice CD player');
        $productDTO1->setStock(11);
        $productDTO1->setCost('50.12');
        $productDTO1->setDiscontinued('yes');

        $productDTO2 = $this->createMock(ProductDTO::class);
        $productDTO2->setCode('P0027');
        $productDTO2->setName('VCR');
        $productDTO2->setDescription('Plays videos');
        $productDTO2->setStock(34);
        $productDTO2->setCost('1200.03');
        $productDTO2->setDiscontinued('yes');

        $productDTO3 = $this->createMock(ProductDTO::class);
        $productDTO3->setCode('P0017');
        $productDTO3->setName('CPU');
        $productDTO3->setDescription('Processing power, ideal for multimedia');
        $productDTO3->setStock(4);
        $productDTO3->setCost('4.22');

        $productDTO4 = $this->createMock(ProductDTO::class);
        $productDTO4->setCode('P0015');
        $productDTO4->setName('Bluray Player');
        $productDTO4->setDescription('Excellent picture');
        $productDTO4->setStock(32);
        $productDTO4->setCost('4.33');

        $productDTO5 = $this->createMock(ProductDTO::class);
        $productDTO5->setCode('P0011');
        $productDTO5->setName('Misc Cables');
        $productDTO5->setDescription('error in export');

        $productFilterService = $this->createMock(ProductFilterService::class);

        $productInfo = new \App\Service\ProductInfo();
        $productInfo->setTotalRowsQuantity(5);

        $productInfo->addFilteredRow($productDTO1);

        $productInfo->addSkippedRow($productDTO2);
        $productInfo->addSkippedRow($productDTO3);
        $productInfo->addSkippedRow($productDTO4);
        $productInfo->addSkippedRow($productDTO5);

        $DTOs = [$productDTO1, $productDTO2, $productDTO3, $productDTO4, $productDTO5,];

        $productFilterService
            ->expects($this->once())
            ->method('filter')
            ->with($DTOs)
            ->willReturn($productInfo);

        $productInfo = $productFilterService->filter($DTOs);

        $this->assertEquals(5, $productInfo->getTotalRowsQuantity());
        $this->assertEquals(1, $productInfo->getRowsSuccessfullyImported());
        $this->assertCount(4, $productInfo->getSkippedRowsAsArray());
    }
}
