<?php

namespace RssParser;

use RssParser\Exceptions\UnknownField;
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
     * @param bool $handle_unknown_fields
     * @return static|null
     * @throws UnknownField
     */
    public static function make(SimpleXMLElement $data, bool $handle_unknown_fields = true): ?self
    {
        $data_string = Parser::filterString($data);
        if ($data_string === null) {
            return null;
        }

        $category = new self($data_string);

        $attributes = $data->attributes();
        foreach ($attributes as $name => $value) {
            switch ($name) {
                case 'domain':
                    $category->domain = Parser::filterString($value);
                    break;

                default:
                    if ($handle_unknown_fields) {
                        throw new UnknownField(self::class, $name, $value);
                    }
            }
        }

        return $category;
    }

}