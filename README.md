# Files

[![Build Status](https://circleci.com/gh/Ekman/php-files.svg?style=svg)](https://app.circleci.com/pipelines/github/Ekman/php-files)
[![Coverage Status](https://coveralls.io/repos/github/Ekman/php-files/badge.svg?branch=master)](https://coveralls.io/github/Ekman/php-files?branch=master)

When creating code that read and writes files I tend to utilize [`Generator`/`Traversable`/`iterable`](https://www.php.net/manual/en/language.generators.overview.php). No need to read
entire files into memory, may as well process them line-by-line. Purely a win-win situation. This library contains code
that I found myself writing over and over again. It is a very small and minimal library.

This library does not re-invent any wheels. It is basically just a wrapper for using [`fopen`](https://www.php.net/manual/en/function.fopen), [`fgets`](https://www.php.net/manual/en/function.fgets), [`fgetcsv`](https://www.php.net/manual/en/function.fgetcsv) and [`fclose`](https://www.php.net/manual/en/function.fclose)
with [`Generator`](https://www.php.net/manual/en/language.generators.overview.php).

## Installation

Install with [Composer](https://getcomposer.org/):

```bash
composer require nekman/php-files
```

## Usage

The library does not contain any classes that needs to be instantiated. It is just a couple of pure functions.

### Text files

To read and write ordinary text files:

```php
use function Nekman\Files\file_read_lines;
use function Nekman\Files\file_write_lines;

# Reading a file
$it = file_read_lines("/path/to/my/file");
foreach ($it as $line) {
	// This is ready, line-by-line. The file is never read completely into memory at once.
	echo $line;
}

# Writing a file
file_write_lines("/path/to/my/file", ["line1", "line2"]);

# Writing a file using Generator
function my_generator(): iterable
{
	yield "line1";
	yield "line2";
}

file_write_lines("/path/to/my/file", my_generator());
```

### CSV files

To read and write CSV files:

```php
use function Nekman\Files\file_read_csv;
use function Nekman\Files\file_write_csv;

# Reading from a CSV
$it = file_read_csv("/path/to/csv");
foreach ($it as $row) {
	[$column1, $column2] = $row;
	echo "$column1,$column2";
}

# Writing a CSV file
file_write_csv("/path/to/csv", [["column1x", "column2x"], ["column1y", "column2y"]]);

# Writing a file using Generator
function my_generator(): iterable
{
	yield ["column1x", "column2x"];
	yield ["column1y", "column2y"];
}

file_write_csv("/path/to/my/file", my_generator());
```

## Versioning

This project complies with [Semantic Versioning](https://semver.org/).

## Changelog

For a complete list of changes, and how to migrate between major versions,
see [releases page](https://github.com/Ekman/luhn-algorithm/releases).
