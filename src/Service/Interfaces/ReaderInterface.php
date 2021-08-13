<?php

declare(strict_types=1);

namespace App\Service\Interfaces;

interface ReaderInterface
{
    function read(?string $filePath): array;
}
