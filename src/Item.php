<?php

namespace RssParser;

use DateTime;
use RssParser\Exceptions\Error;
use RssParser\Exceptions\UnknownField;
use SimpleXMLElement;
use Exception;

/**
 * Class Item
 *
 * @package RssParser
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Item
{
    /**
     * @var string The URL of the item
     */
    public $link;

    /**
     * @var string|null
     */
    public $pda_link;

    /**
     * @var string|null
     */
    public $amp_link;

    /**
     * @var string|null
     */
    public $id;

    /**
     * @var string|null The title of the item
     */
    public $title;

    /**
     * @var string|null The item synopsis
     */
    public $description;

    /**
     * @var string|null
     */
    public $full_text;

    /**
     * @var string|null
     */
    public $content;

    /**
     * @var string|null A string that uniquely identifies the item
     */
    public $guid;

    /**
     * @var DateTime|null Indicates when the item was published
     */
    public $pub_date;

    /**
     * @var string|null
     */
    public $pub_date_string;

    /**
     * @var DateTime|null The last time the item changed
     */
    public $last_update_date;

    /**
     * @var string|null
     */
    public $last_update_date_string;

    /**
     * @var Category[] Includes the item in one or more categories
     */
    public $categories = [];

    /**
     * @var string|null
     */
    public $block;

    /**
     * @var string|null Email address of the author of the item
     */
    public $author;

    /**
     * @var Enclosure[] Describes a media object that is attached to the item
     */
    public $enclosure = [];

    /**
     * @var Link|null Indicates when the item was published
     */
    public $source;

    /**
     * @var string|null URL of a page for comments relating to the item
     */
    public $comments;

    /**
     * @var string|null
     */
    public $image;

    /**
     * @var string|null
     */
    public $thumbnail;

    /**
     * Item constructor.
     *
     * @param string $link
     */
    public function __construct(string $link)
    {
        $this->link = $link;
    }

    /**
     * @param SimpleXMLElement $item_data
     * @return static|null
     * @throws Error
     * @throws UnknownField
     */
    public static function make(SimpleXMLElement $item_data): ?self
    {
        if (empty($item_data->link)) {
            return null;
        }

        $link = trim((string)$item_data->link);
        if ($link === '') {
            return null;
        }

        $item = new self($link);

        foreach ($item_data as $name => $value) {
            $value_string = Parser::filterString($value);
            switch ($name) {
                case 'id':
                case 'post-id':
                    $item->id = $value_string;
                    break;

                case 'pdalink':
                case 'pda-link':
                case 'pdaLink':
                case 'pda_link':
                    $item->pda_link = $value_string;
                    break;

                case 'amplink':
                case 'ampLink':
                case 'amp_link':
                case 'amp-link':
                    $item->amp_link = $value_string;
                    break;

                case 'title':
                    $item->title = $value_string;
                    break;

                case 'description':
                    $item->description = $value_string;
                    break;

                case 'guid':
                    $item->guid = $value_string;
                    break;

                case 'pubDate':
                case 'pubdate':
                case 'pub_date':
                case 'pub-date':
                    if (!empty($value_string)) {
                        try {
                            $item->pub_date = new DateTime($value_string);
                        } catch (Exception $e) {
                            $item->pub_date_string = $value_string;
                        }
                    }
                    break;

                case 'updateTime':
                case 'updatetime':
                case 'update-time':
                case 'update_time':
                    if (!empty($value_string)) {
                        try {
                            $item->last_update_date = new DateTime($value_string);
                        } catch (Exception $e) {
                            $item->last_update_date_string = $value_string;
                        }
                    }
                    break;

                case 'author':
                    $item->author = $value_string;
                    break;

                case 'enclosure':
                    foreach ($item_data->enclosure as $enclosure_data) {
                        $enclosure = Enclosure::make($enclosure_data);
                        if ($enclosure !== null) {
                            $item->enclosure[] = $enclosure;
                        }
                    }
                    break;

                case 'fulltext':
                case 'full-text':
                case 'full_text':
                case 'fullText':
                case 'yandex:full-text':
                    $item->full_text = $value_string;
                    break;

                case 'content':
                    $item->content = $value_string;
                    break;

                case 'source':
                    if (isset($value->attributes()['url'])) {
                        $item->source = new Link($value->attributes()['url'], $value_string);
                    } else {
                        throw new Error('Source has not url');
                    }
                    break;

                case 'comments':
                    $item->comments = $value_string;
                    break;

                case 'category':
                    foreach ($item_data->category as $category) {
                        $item->categories[] = Category::make($category);
                    }
                    break;

                case 'block':
                    $item->block = $value_string;
                    break;

                case 'image':
                    $item->image = $value_string;
                    break;

                case 'thumbnail':
                    $item->thumbnail = $value_string;
                    break;

                case 'site':
                case 'link':
                    break;

                default:
                    throw new UnknownField(self::class, $name, $value);
            }
        }

        return $item;
    }
}