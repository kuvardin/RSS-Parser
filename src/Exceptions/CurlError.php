<?php

namespace RssParser\Exceptions;

use Exception;

/**
 * Class CurlError
 *
 * @package RssParser\Exceptions
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class CurlError extends Exception
{
    /**
     * @param resource $ch
     * @return static
     */
    public static function make($ch): self
    {
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        curl_close($ch);
        return new self($curl_error, $curl_errno);
    }

}