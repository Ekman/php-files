<?php

namespace Nekman\Files;

use Nekman\Files\Exceptions\FileNotFoundException;
use Nekman\Files\Exceptions\FileNotReadableException;
use Nekman\Files\Exceptions\FilesException;

/**
 * Ensure that a file exists and is readable
 * @param string $filePath The file to check
 * @throws FileNotFoundException If the file could not be found
 * @throws FileNotReadableException If the file is not readable
 * @throws FilesException
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
            yield trim($line);
        }
    } finally {
        @fclose($stream);
    }
}

/**
 * Read CSV rows from a file
 * @throws FileNotReadableException If the file is not readable
 * @throws FileNotFoundException If the file does not exist
 * @throws FilesException
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
 * Recursively creates the directory if it does not exist
 * @param string $dirPath The directory path to create
 * @throws FilesException
 */
function create_directory_if_not_exists(string $dirPath): void
{
    if (!file_exists($dirPath)) {
        mkdir($dirPath, 0777, true);
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
    create_directory_if_not_exists(basename($filePath));

    $stream = fopen($filePath, "w+b");

    try {
        foreach ($lines as $line) {
            fputs($stream, $line . PHP_EOL);
        }
    } finally {
        @fclose($stream);
    }
}

/**
 * Write CSV to a file
 * @param string $filePath Path to the file to write to
 * @param iterable $rows Each row being an array to write to the file
 * @param string $separator
 * @param string $enclosure
 * @param string $escape
 * @throws FilesException
 */
function file_write_csv(
    string $filePath,
    iterable $rows,
    string $separator = ',',
    string $enclosure = '"',
    string $escape = '\\'
): void {
    create_directory_if_not_exists(basename($filePath));

    $stream = fopen($filePath, "w+b");

    try {
        foreach ($rows as $row) {
            fputcsv($stream, $row, $separator, $enclosure, $escape);
        }
    } finally {
        @fclose($stream);
    }
}
