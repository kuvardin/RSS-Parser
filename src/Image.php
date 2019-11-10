<?php

namespace RssParser;

use RssParser\Exceptions\Error;
use RssParser\Exceptions\UnknownField;
use SimpleXMLElement;

/**
 * <image> is an optional sub-element of <channel>, which contains three required
 * and three optional sub-elements
 *
 * @package RssParser
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Image
{
    /**
     * @var string Is the URL of a GIF, JPEG or PNG image that represents the channel
     */
    public $url;

    /**
     * @var string|null Describes the image, it's used in the ALT attribute of the HTML <img> tag
     * when the channel is rendered in HTML
     */
    public $title;

    /**
     * @var string|null Contains text that is included in the TITLE attribute of the link formed
     * around the image in the HTML rendering
     */
    public $description;

    /**
     * @var string|null Is the URL of the site, when the channel is rendered, the image is a link
     * to the site. (Note, in practice the image <title> and <link> should have the same value
     * as the channel's <title> and <link>
     */
    public $link;

    /**
     * @var int|null Indicating the width of the image in pixels
     */
    public $width;

    /**
     * @var int|null Indicating the height of the image in pixels
     */
    public $height;

    /**
     * Image constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @param SimpleXMLElement $data
     * @return static|null
     * @throws UnknownField
     */
    public static function make(SimpleXMLElement $data): ?self
    {
        if (empty($data->url)) {
            return null;
        }

        $url = Parser::filterString($data->url);
        if ($url === null) {
            return null;
        }

        $image = new self($url);

        foreach ($data as $name => $value) {
            $value_string = Parser::filterString($value);
            switch ($name) {
                case 'url':
                    break;

                case 'title':
                    $image->title = $value_string;
                    break;

                case 'description':
                    $image->description = $value_string;
                    break;

                case 'link':
                    $image->link = $value_string;
                    break;

                case 'width':
                    $image->width = Parser::filterInt($value, true);
                    break;

                case 'height':
                    $image->height = Parser::filterInt($value, true);
                    break;

                default:
                    throw new UnknownField(self::class, $name, $value);
            }
        }

        return $image;
    }
}