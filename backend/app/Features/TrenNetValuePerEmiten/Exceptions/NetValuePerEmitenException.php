<?php
namespace App\Features\TrenNetValuePerEmiten\Exceptions;

use RuntimeException;
use Throwable;

final class NetValuePerEmitenException extends RuntimeException
{
    public function __construct($message = "Failed to get net value per emiten", $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
?>