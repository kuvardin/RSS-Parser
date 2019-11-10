<?php

namespace RssParser\Exceptions;

use Exception;

/**
 * Class Error
 *
 * @package RssParser\Exceptions
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Error extends Exception
{
    public const XML_PARSING_ERROR = 'Parsing error';
    public const IS_NOT_XML = 'Is not XML page';
}