<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\ProductDTO;
use App\Service\DatabaseSavingService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DatabaseSavingServiceTest extends TestCase
{
    public function testStore()
    {
        $productDTO1 = new ProductDTO();
        $productDTO1->setCode('P0001');
        $productDTO1->setName('TV');
        $productDTO1->setDescription('32” Tv');
        $productDTO1->setStock(10);
        $productDTO1->setCost('399.99');

        $productDTO2 = new ProductDTO();
        $productDTO2->setCode('P0002');
        $productDTO2->setName('Cd Player');
        $productDTO2->setDescription('Nice CD player');
        $productDTO2->setStock(11);
        $productDTO2->setCost('50.12');
        $productDTO2->setDiscontinued('yes');

        $DTOs = [$productDTO1, $productDTO2,];

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $databaseSavingService = new DatabaseSavingService($entityManager);

        $productData = $databaseSavingService->store($DTOs);

        $this->assertCount(2, $productData);
        $this->assertSame('P0001', $productData[0]->getProductCode());
        $this->assertSame('TV', $productData[0]->getProductName());
        $this->assertSame('32” Tv', $productData[0]->getProductDesc());
        $this->assertSame(10, $productData[0]->getStock());
        $this->assertSame('399.99', $productData[0]->getCost());
        $this->assertNull($productData[0]->getDiscontinued());
        $this->assertSame('P0002', $productData[1]->getProductCode());
        $this->assertSame('Cd Player', $productData[1]->getProductName());
        $this->assertSame('Nice CD player', $productData[1]->getProductDesc());
        $this->assertSame(11, $productData[1]->getStock());
        $this->assertSame('50.12', $productData[1]->getCost());
        $this->assertNotNull($productData[1]->getDiscontinued());
    }
}
