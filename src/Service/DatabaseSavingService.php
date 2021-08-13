<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDTO;
use App\Entity\ProductData;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseSavingService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ProductDTO[] $data
     */
    public function store(array $data): void
    {
        foreach ($data as $productDTO) {
            $productData = new ProductData();
            $productData->setProductCode($productDTO->getCode());
            $productData->setProductName($productDTO->getName());
            $productData->setProductDesc($productDTO->getDescription());
            $productData->setCost($productDTO->getCost());
            $productData->setStock($productDTO->getStock());
            $productData->setDiscontinued((empty($productDTO->getDiscontinued()) || mb_strtolower(trim($productDTO->getDiscontinued())) === 'no') ? null : new \DateTime());
            $this->entityManager->persist($productData);
        }

        $this->entityManager->flush();
    }
}
