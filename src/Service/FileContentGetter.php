<?php

declare(strict_types=1);

namespace App\Service;

class FileContentGetter
{
    /**
     * @param string $filePath
     * @return string
     */
    public function getContent(string $filePath): string
    {
        if (empty($filePath)) {
            throw new \LogicException('Path to CSV file is required and must be valid');
        }

        $filePath = str_replace("\\", '/', $filePath);

        if (!is_file($filePath)) {
            throw new \LogicException('Invalid path to file');
        }

        $data = file_get_contents($filePath);

        if ($data === false) {
            throw new \LogicException('Unable to open the file of "'.$filePath.'" for reading');
        }

        return $data;
    }
}
