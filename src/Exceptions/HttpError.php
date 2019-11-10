<?php

namespace RssParser\Exceptions;

use Exception;
use Throwable;

/**
 * Class HttpError
 *
 * @package RssParser\Exceptions
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class HttpError extends Exception
{
    /**
     * HttpError constructor.
     *
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(int $code, Throwable $previous = null)
    {
        $message = "HTTP error #$code";
        parent::__construct($message, $code, $previous);
    }
}