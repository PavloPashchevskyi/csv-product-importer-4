<?php

declare(strict_types=1);

namespace App\Handler;

use App\DTO\ProductDTO;
use App\Repository\ProductDataRepository;

class ProductDataHandler
{
    /**
     * @var ProductDataRepository
     */
    private $productDataRepository;

    /**
     * @param ProductDataRepository $productDataRepository
     */
    public function __construct(ProductDataRepository $productDataRepository)
    {
        $this->productDataRepository = $productDataRepository;
    }

    /**
     * @param ProductDTO $productDTO
     * @param bool $saveImmediately
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(ProductDTO $productDTO, bool $saveImmediately = false): void
    {
        $productData = $productDTO->getParentEntity();
        $productData->setStock($productDTO->getStock());
        $productData->setCost($productDTO->getCost());
        $productData->setDiscontinued((empty($productDTO->getDiscontinued()) || trim(mb_strtolower($productDTO->getDiscontinued())) === 'no') ? null : new \DateTime());
        $this->productDataRepository->preSave($productData);
        if ($saveImmediately === true) {
            $this->productDataRepository->save();
        }
    }
}
