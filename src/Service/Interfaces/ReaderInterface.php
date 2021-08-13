<?php

declare(strict_types=1);

namespace App\Service\Interfaces;

interface ReaderInterface
{
    /**
     * @param string|null $filePath
     * @return array
     */
    function read(?string $filePath): array;
}
