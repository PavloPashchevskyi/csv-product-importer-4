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
        $productDTO1 = new ProductDTO();
        $productDTO1->setCode('P0002');
        $productDTO1->setName('Cd Player');
        $productDTO1->setDescription('Nice CD player');
        $productDTO1->setStock(11);
        $productDTO1->setCost('50.12');
        $productDTO1->setDiscontinued('yes');

        $productDTO2 = new ProductDTO();
        $productDTO2->setCode('P0027');
        $productDTO2->setName('VCR');
        $productDTO2->setDescription('Plays videos');
        $productDTO2->setStock(34);
        $productDTO2->setCost('1200.03');
        $productDTO2->setDiscontinued('yes');

        $productDTO3 = new ProductDTO();
        $productDTO3->setCode('P0017');
        $productDTO3->setName('CPU');
        $productDTO3->setDescription('Processing power, ideal for multimedia');
        $productDTO3->setStock(4);
        $productDTO3->setCost('4.22');

        $productDTO4 = new ProductDTO();
        $productDTO4->setCode('P0015');
        $productDTO4->setName('Bluray Player');
        $productDTO4->setDescription('Excellent picture');
        $productDTO4->setStock(32);
        $productDTO4->setCost('4.33');

        $productDTO5 = new ProductDTO();
        $productDTO5->setCode('P0011');
        $productDTO5->setName('Misc Cables');
        $productDTO5->setDescription('error in export');

        $productDTO6 = new ProductDTO();
        $productDTO6->setCode('P0002');
        $productDTO6->setName('Cd Player');
        $productDTO6->setDescription('Nice CD player');
        $productDTO6->setStock(11);
        $productDTO6->setCost('50.12');
        $productDTO6->setDiscontinued('yes');

        $productFilterService = new ProductFilterService();

        $DTOs = [$productDTO1, $productDTO2, $productDTO3, $productDTO4, $productDTO5, $productDTO6,];

        $productInfo = $productFilterService->filter($DTOs);

        $this->assertEquals(6, $productInfo->getTotalRowsQuantity());
        $this->assertEquals(2, $productInfo->getRowsSuccessfullyImported());
        $this->assertCount(4, $productInfo->getSkippedRowsAsArray());

        $filteredRows = $productInfo->getFilteredRowsContent();
        $this->assertSame('P0002', $filteredRows[0]->getCode());
        $this->assertSame('P0015', $filteredRows[1]->getCode());

        $skippedRows = $productInfo->getSkippedRowsAsArray();
        $this->assertSame('P0027', $skippedRows[0][0]);
        $this->assertSame('P0017', $skippedRows[1][0]);
        $this->assertSame('P0011', $skippedRows[2][0]);

        $dualRowsNumbers = $productInfo->getDualRowsNumbers();
        $this->assertCount(1,$dualRowsNumbers);
        $this->assertSame('P0002', $skippedRows[3][0]);
    }
}
