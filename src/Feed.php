<?php

namespace RssParser;

use DateTime;
use Exception;
use RssParser\Exceptions\Error;
use RssParser\Exceptions\UnknownField;

/**
 * Class Feed
 *
 * @package RssParser
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Feed
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string|null
     */
    public $id;

    /**
     * @var Item[]
     */
    public $items = [];

    /**
     * @var array
     */
    public $curl_info;

    /**
     * @var string|null
     */
    public $xml_version;

    /**
     * @var string|null The URL to the HTML website corresponding to the channel
     */
    public $link;

    /**
     * @var string|null The name of the channel
     */
    public $title;

    /**
     * @var string|null
     */
    public $subtitle;

    /**
     * @var string|null Phrase or sentence describing the channel
     */
    public $description;

    /**
     * @var string|null
     */
    public $about;

    /**
     * @var string|null Email address for person responsible for technical issues relating to channel
     */
    public $webmaster;

    /**
     * @var Image|null Specifies a GIF, JPEG or PNG image that can be displayed with the channel
     */
    public $image;

    /**
     * @var DateTime|null The publication date for the content in the channel.
     * For example, the New York Times publishes on a daily basis, the publication date flips once
     * every 24 hours. That's when the pubDate of the channel changes
     */
    public $pub_date;

    /**
     * @var string|null
     */
    public $pub_date_string;

    /**
     * @var DateTime|null The last time the content of the channel changed
     */
    public $last_build_date;

    /**
     * @var string|null
     */
    public $last_build_date_string;

    /**
     * @var string|null The language the channel is written in
     */
    public $language;

    /**
     * @var string|null Copyright notice for content in the channel
     */
    public $copyright;

    /**
     * @var int|null ttl stands for time to live. It's a number of minutes that indicates
     * how long a channel can be cached before refreshing from the source
     */
    public $ttl;

    /**
     * @var string|null A string indicating the program used to generate the channel
     */
    public $generator;

    /**
     * @var string|null A URL that points to the documentation for the format used in the RSS file.
     * It's probably a pointer to this page. It's for people who might stumble across an RSS file
     * on a Web server 25 years from now and wonder what it is.
     */
    public $docs;

    /**
     * @var string|null Email address for person responsible for editorial content
     */
    public $managing_editor;

    /**
     * @var Category[] Specify one or more categories that the channel belongs to. Follows
     * the same rules as the <item>-level category element
     */
    public $categories = [];

    /**
     * Feed constructor.
     *
     * @param string $url
     * @param array $curl_info
     */
    private function __construct(string $url, array $curl_info)
    {
        $this->url = $url;
        $this->curl_info = $curl_info;
    }

    /**
     * @param string $url
     * @param string $response
     * @param array $curl_info
     * @param bool $handle_unknown_fields
     * @return static
     * @throws Error
     * @throws UnknownField
     */
    public static function make(string $url, string $response, array $curl_info, bool $handle_unknown_fields = true): self
    {
//        $response = preg_replace('#<(\w+):(\w+)#u', '<$1___$2', $response);
//        $response = preg_replace('#</(\w+):(\w+)#u', '</$1___$2', $response);
        $f = fopen('cache.txt', 'w');
        fwrite($f, print_r($response, true));
        fclose($f);

        $xml = simplexml_load_string($response);
        if ($xml === false) {
            $response = preg_replace('|^(.*?)<\?xml|us', '<?xml', $response);
            $xml = simplexml_load_string($response, 'SimpleXMLElement');
            if ($xml === false) {
                if (mb_strpos($response, '<?xml') !== 0) {
                    throw new Error(Error::IS_NOT_XML);
                }
                throw new Error(Error::XML_PARSING_ERROR);
            }
        }

        $f = fopen('parsed.txt', 'w');
        fwrite($f, print_r($xml, true));
        fclose($f);

        $feed = new self($url, $curl_info);

        $attributes = $xml->attributes();
        if (!empty($attributes->version)) {
            $feed->xml_version = (string)$attributes->version;
        }

        if (!empty($xml->channel)) {
            $channel = $xml->channel->children();
        } else {
            $channel = $xml->children();
        }

        foreach ($channel as $name => $value) {
            $value_string = Parser::filterString($value);

            switch ($name) {
                case 'link':
                    $feed->link = $value_string;
                    break;

                case 'title':
                    $feed->title = $value_string;
                    break;

                case 'description':
                    $feed->description = $value_string;
                    break;

                case 'about':
                    $feed->about = $value_string;
                    break;

                case 'image':
                    $feed->image = Image::make($value, $handle_unknown_fields);
                    break;

                case 'id':
                    $feed->id = $value_string;
                    break;

                case 'subtitle':
                case 'subTitle':
                    $feed->subtitle = $value_string;
                    break;

                case 'webMaster':
                case 'webmaster':
                case 'web-master':
                case 'web_master':
                    $feed->webmaster = $value_string;
                    break;

                case 'pubDate':
                case 'pubdate':
                case 'pub_date':
                case 'pub-date':
                    try {
                        $feed->pub_date = new DateTime($value_string);
                    } catch (Exception $e) {
                        $feed->pub_date_string = $value_string;
                    }
                    break;

                case 'lastBuildDate':
                case 'lastbuilddate':
                case 'last-build-date':
                case 'last_build_date':
                    try {
                        $feed->last_build_date = new DateTime($value_string);
                    } catch (Exception $e) {
                        $feed->last_build_date_string = $value_string;
                    }
                    break;

                case 'language':
                    $feed->language = $value_string;
                    break;

                case 'copyright':
                    $feed->copyright = $value_string;
                    break;

                case 'ttl':
                case 'TTL':
                    $feed->ttl = Parser::filterInt($value, true);
                    break;

                case 'generator':
                    $feed->generator = $value_string;
                    break;

                case 'docs':
                    $feed->docs = $value_string;
                    break;

                case 'managingEditor':
                case 'managingeditor':
                case 'managing-editor':
                case 'managing_editor':
                    $feed->managing_editor = $value_string;
                    break;

                case 'item':
                    foreach ($channel->item as $item_data) {
                        $item = Item::make($item_data, $handle_unknown_fields);
                        if ($item !== null) {
                            $feed->items[] = $item;
                        }
                    }
                    break;

                case 'category':
                    foreach ($channel->category as $item_data) {
                        $category = Category::make($item_data);
                        if ($category !== null) {
                            $feed->categories[] = $category;
                        }
                    }
                    if (count($feed->categories) > 1) {
                        print_r($feed->categories);
                        exit;
                    }
                    break;

                case 'site':
                case 'meta':
                    break;

                default:
                    if ($handle_unknown_fields) {
                        throw new UnknownField(self::class, $name, $value);
                    }
            }
        }

        return $feed;
    }
}