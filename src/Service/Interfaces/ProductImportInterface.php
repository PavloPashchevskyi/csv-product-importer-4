<?php

declare(strict_types=1);

namespace App\Service\Interfaces;

interface ProductImportInterface
{
    const CSV_COLUMN_PRODUCT_CODE = 'Product Code';
    const CSV_COLUMN_PRODUCT_NAME = 'Product Name';
    const CSV_COLUMN_PRODUCT_DESCRIPTION = 'Product Description';
    const CSV_COLUMN_STOCK = 'Stock';
    const CSV_COLUMN_COST = 'Cost in GBP';
    const CSV_COLUMN_DISCONTINUED = 'Discontinued';
}
