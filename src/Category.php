<?php

namespace RssParser;

use SimpleXMLElement;

/**
 * Class Category
 *
 * @package RssParser
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Category
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $domain;

    /**
     * Category constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param SimpleXMLElement $data
     * @return static|null
     */
    public static function make(SimpleXMLElement $data): ?self
    {
        $data_string = Parser::filterString($data);
        if ($data_string === null) {
            return null;
        }

        $category = new self($data_string);

        $attributes = $data->attributes();
        if ($attributes !== null && !empty($attributes->domain)) {
            $category->domain = Parser::filterString($attributes->domain);
        }

        return $category;
    }

}