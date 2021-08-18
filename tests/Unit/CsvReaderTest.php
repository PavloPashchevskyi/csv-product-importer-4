<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\ProductDTO;
use App\Service\CsvReader;
use App\Service\FileContentGetter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class CsvReaderTest extends TestCase
{
    public function testReadReturnsArrayWithDtoObjectWhenGivenValidFile()
    {
        $fileContentGetter = $this->createMock(FileContentGetter::class);
        $data = '
        Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued
        P0002,Cd Player,Nice CD player,11,50.12,yes
        ';
        $fileContentGetter
            ->expects($this->once())
            ->method('getContent')
            ->with('/some/path/to/csv/file.csv')
            ->willReturn($data);
        $decoder = $this->createMock(DecoderInterface::class);

        $productDTO = new ProductDTO();

        $productDTO->setCode('P0002');
        $productDTO->setName('Cd Player');
        $productDTO->setDescription('Nice CD player');
        $productDTO->setStock(11);
        $productDTO->setCost('50.12');
        $productDTO->setDiscontinued('yes');

        $decoder
            ->expects($this->once())->method('decode')
            ->with($data)
            ->willReturn([
                [
                    'Product Code' => 'P0002',
                    'Product Name' => 'Cd Player',
                    'Product Description' => 'Nice CD player',
                    'Stock' => '11',
                    'Cost in GBP' => '50.12',
                    'Discontinued' => 'yes',
                ]
            ]);

        $csvReader = new CsvReader($fileContentGetter, $decoder);

        $DTOs = $csvReader->read('/some/path/to/csv/file.csv');

        $this->assertCount(1, $DTOs);
        $this->assertInstanceOf(ProductDTO::class, $DTOs[0]);
        $this->assertEquals($productDTO, $DTOs[0]);
    }
}
