<?php

namespace RssParser\Exceptions;

use Exception;
use Throwable;
use SimpleXMLElement;

/**
 * Class UnknownField
 *
 * @package RssParser\Exceptions
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class UnknownField extends Exception
{
    /**
     * @var string
     */
    protected $class_name;

    /**
     * @var string
     */
    protected $field_name;

    /**
     * @var SimpleXMLElement
     */
    protected $field_value;

    /**
     * UnknownField constructor.
     *
     * @param string $class_name
     * @param string $field_name
     * @param SimpleXMLElement $field_value
     * @param Throwable|null $previous
     */
    public function __construct(string $class_name, string $field_name, SimpleXMLElement $field_value, Throwable $previous = null)
    {
        $message = "Unknown field $field_name in class $class_name with value $field_value";
        if ($field_value->attributes() !== null) {
            $message .= ' and attributes ' . print_r($field_value->attributes(), true);
        }

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->class_name;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->field_name;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getFieldValue(): SimpleXMLElement
    {
        return $this->field_value;
    }
}