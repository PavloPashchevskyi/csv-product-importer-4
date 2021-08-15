<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\ProductDTO;
use App\Entity\ProductData;
use App\Service\DatabaseSavingService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DatabaseSavingServiceTest extends TestCase
{
    public function testStore()
    {
        $productDTO1 = $this->createMock(ProductDTO::class);
        $productDTO1->setCode('P0001');
        $productDTO1->setName('TV');
        $productDTO1->setDescription('32” Tv');
        $productDTO1->setStock(10);
        $productDTO1->setCost('399.99');

        $productDTO2 = $this->createMock(ProductDTO::class);
        $productDTO2->setCode('P0002');
        $productDTO2->setName('Cd Player');
        $productDTO2->setDescription('Nice CD player');
        $productDTO2->setStock(11);
        $productDTO2->setCost('50.12');
        $productDTO2->setDiscontinued('yes');

        $DTOs = [$productDTO1, $productDTO2,];

        $databaseSavingService = $this->createMock(DatabaseSavingService::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $productData1 = $this->createMock(ProductData::class);
        $productData1->setProductCode('P0001');
        $productData1->setProductName('TV');
        $productData1->setProductDesc('32” Tv');
        $productData1->setStock(10);
        $productData1->setCost('399.99');
        $entityManager->persist($productData1);

        $productData2 = $this->createMock(ProductData::class);
        $productData2->setProductCode('P0002');
        $productData2->setProductName('Cd Player');
        $productData2->setProductDesc('Nice CD player');
        $productData2->setStock(11);
        $productData2->setCost('50.12');
        $productData2->setDiscontinued((empty($productDTO2->getDiscontinued()) || mb_strtolower(trim($productDTO2->getDiscontinued())) === 'no') ? null : new \DateTime());
        $entityManager->persist($productData2);

        $entityManager->flush();

        $productData = [$productData1, $productData2];

        $databaseSavingService->store($DTOs);

        $this->assertCount(2, $productData);
    }
}
