<?php

declare (strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Unique()
     * @Assert\Regex(pattern="/^P\d{4,}$/", message="Product code is not valid")
     */
    private $code;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var int
     * @Assert\PositiveOrZero(message="Stock ID could not be negative")
     */
    private $stock;

    /**
     * @var string
     * @Assert\Currency(message="Cost could not be non-money value")
     */
    private $cost;

    /**
     * @var string
     * @Assert\Type(type="string", message="Discontinued could be only "yes" or "no" value")
     */
    private $discontinued;

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int|null
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * @param int|null $stock
     */
    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    /**
     * @return string|null
     */
    public function getCost(): ?string
    {
        return $this->cost;
    }

    /**
     * @param string|null $cost
     */
    public function setCost(?string $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return string|null
     */
    public function getDiscontinued(): ?string
    {
        return $this->discontinued;
    }

    /**
     * @param string|null $discontinued
     */
    public function setDiscontinued(?string $discontinued): void
    {
        $this->discontinued = $discontinued;
    }
}
