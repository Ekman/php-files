<?php

namespace Nekman\Files;

use Nekman\EsPagination\Exceptions\FileNotFoundException;
use Nekman\EsPagination\Exceptions\FileNotReadableException;
use Nekman\EsPagination\Exceptions\FilesException;

/**
 * Ensure that a file exists and is readable
 * @param string $filePath The file to check
 * @throws FileNotFoundException If the file could not be found
 * @throws FileNotReadableException If the file is not readable
 */
function ensure_file_exists_and_readable(string $filePath): void
{
    if (!file_exists($filePath)) {
        throw new FileNotFoundException("File \"$filePath\" does not exist");
    }

    if (!is_readable($filePath)) {
        throw new FileNotReadableException("File \"$filePath\" is not readable");
    }
}

/**
 * Read lines from a file
 * @param string $filePath The file to read lines from
 * @return iterable|string[] Each line from the file
 * @throws FileNotFoundException If the file could not be found
 * @throws FileNotReadableException If the file is not readable
 * @throws FilesException
 */
function file_read_lines(string $filePath): iterable
{
    ensure_file_exists_and_readable($filePath);

    $stream = fopen($filePath, "rb");

    try {
        while ($line = fgets($stream)) {
            yield $line;
        }
    } finally {
        @fclose($stream);
    }
}

/**
 * Read CSV rows from a file
 * @throws FileNotReadableException
 * @throws FileNotFoundException
 */
function file_read_csv(
    string $csvPath,
    string $separator = ',',
    string $enclosure = '"',
    string $escape = '\\'
): iterable {
    ensure_file_exists_and_readable($csvPath);

    $stream = fopen($csvPath, "rb");

    try {
        while ($row = fgetcsv($stream, null, $separator, $enclosure, $escape)) {
            yield $row;
        }
    } finally {
        @fclose($stream);
    }
}

/**
 * Write lines to a file. Will create parent directories if they do not exist.
 * @param string $filePath The file to write to
 * @param iterable|string[] $lines
 * @throws FilesException
 */
function file_write_lines(string $filePath, iterable $lines): void
{
    $directory = basename($filePath);

    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    $stream = fopen($filePath, "w+b");

    try {
        foreach ($lines as $line) {
            fputs($stream, $line . PHP_EOL);
        }
    } finally {
        @fclose($stream);
    }
}
