<?php

namespace RssParser;

use RssParser\Exceptions\Error;
use RssParser\Exceptions\UnknownField;
use SimpleXMLElement;

/**
 * Class Enclosure
 *
 * @package RssParser
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Enclosure
{
    /**
     * @var string Says where the enclosure is located
     */
    public $url;

    /**
     * @var int|null Says how big it is in bytes
     */
    public $length;

    /**
     * @var string|null A standard MIME type
     */
    public $type;

    /**
     * Enclosure constructor.
     *
     * @param string $url
     * @param int|null $length
     * @param string|null $type
     */
    public function __construct(string $url, int $length = null, string $type = null)
    {
        $this->url = $url;
        $this->length = $length;
        $this->type = $type;
    }

    /**
     * @param SimpleXMLElement $data
     * @param bool $handle_unknown_fields
     * @return static|null
     * @throws UnknownField
     */
    public static function make(SimpleXMLElement $data, bool $handle_unknown_fields = true): ?self
    {
        $attributes = $data->attributes();
        if (empty($attributes->url)) {
            return null;
        }

        $url = Parser::filterString($attributes->url);
        if ($url === null) {
            return null;
        }

        $enclosure = new self($url);

        foreach ($attributes as $name => $value) {
            switch ($name) {
                case 'url':
                    break;

                case 'length':
                    $enclosure->length = Parser::filterInt($value, true);
                    break;

                case 'type':
                    $enclosure->type = Parser::filterString($value);
                    break;

                default:
                    if ($handle_unknown_fields) {
                        throw new UnknownField(self::class, $name, $value);
                    }
            }
        }

        return $enclosure;
    }
}