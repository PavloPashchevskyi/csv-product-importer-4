<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CsvReader;
use App\Service\DatabaseSavingService;
use App\Service\ProductFilterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductImportCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:product:import';

    /**
     * @var CsvReader
     */
    private $csvReader;

    /**
     * @var ProductFilterService
     */
    private $productFilterService;

    /**
     * @var DatabaseSavingService
     */
    private $databaseSavingService;

    /**
     * @param CsvReader $csvReader
     * @param ProductFilterService $productFilterService
     * @param DatabaseSavingService $databaseSavingService
     * @param string|null $name
     */
    public function __construct(CsvReader $csvReader, ProductFilterService $productFilterService, DatabaseSavingService $databaseSavingService, string $name = null)
    {
        $this->csvReader = $csvReader;

        $this->databaseSavingService = $databaseSavingService;
        $this->productFilterService = $productFilterService;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Imports products data from CSV file')
            ->setHelp('This command allows you to import products data from comma-separated CSV file')
            ->addOption(
                'filepath',
                'f',
                InputOption::VALUE_REQUIRED,
                'Path to comma-separated CSV-file you are going to import'
            )
            ->addOption(
                'test',
                't',
                null,
                'Reads data from CSV-file, but does not store in database table'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $filePath = $input->getOption('filepath');
            if (empty($filePath)) {
                $filePath = $io->ask('Path to CSV-file', null, function ($path) {
                    if (empty($path)) {
                        throw new Exception('Path to CSV file is required and must be valid');
                    }

                    return (string) $path;
                });
            }

            $io->title('Import from CSV-file...');

            $startTime = microtime(true);
            $DTOs = $this->csvReader->read($filePath);
            $productInfo = $this->productFilterService->filter($DTOs);
            $endTime = microtime(true);
            $csvImportExecTime = $endTime - $startTime;

            $dbStoreTime = 0;

            if (((int) $input->getOption('test')) === 0) {
                $isTestMode = $io->ask('Add imported data to database?', 'yes');

                if (in_array(mb_strtolower($isTestMode), ['y', 'yes'])) {
                    $startTime = microtime(true);
                    $this->databaseSavingService->store($productInfo->getFilteredRowsContent());
                    $endTime = microtime(true);
                    $dbStoreTime = $endTime - $startTime;
                }
            }

            $io->text('CSV-file metadata:');
            $csvImportExecTime += $dbStoreTime;
            $io->table(
                ['CSV-file metadatum', 'value'],
                [
                    ['Import execution time (sec.):', $csvImportExecTime],
                    ['Total rows quantity:', $productInfo->getTotalRowsQuantity()],
                    ['Imported successfully:', $productInfo->getRowsSuccessfullyImported()],
                    ['Skipped:', $productInfo->getSkippedRowsQuantity()],
                ]
            );

            if ($productInfo->getSkippedRowsQuantity() > 0) {
                $io->text('Skipped rows:');
                $io->table(
                    ['ProductCode', 'ProductName', 'ProductDescription', 'Stock', 'Cost', 'Discontinued'],
                    $productInfo->getSkippedRowsAsArray()
                );
            }
        } catch (Exception $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
