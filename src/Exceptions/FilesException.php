<?php

declare(strict_types=1);

namespace Nekman\Files\Exceptions;

use Exception;
use Nekman\Files\Contracts\FilesExceptionInterface;

class FilesException extends Exception implements FilesExceptionInterface
{
}
