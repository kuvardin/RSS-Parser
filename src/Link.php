<?php

namespace RssParser;

/**
 * Class Link
 *
 * @package RssParser
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Link
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string|null
     */
    public $name;

    /**
     * Link constructor.
     *
     * @param string $url
     * @param string|null $name
     */
    public function __construct(string $url, string $name = null)
    {
        $this->url = $url;
        $this->name = $name;
    }

    /**
     * @param string|null $name
     * @return string
     */
    public function getHtml(string $name = null): string
    {
        $name = $name ?? $this->name ?? $this->url;
        return "<a href=\"{$this->url}\">{$name}</a>";
    }

    /**
     * @param string|null $name
     * @return string
     */
    public function getMarkdown(string $name = null): string
    {
        $name = $name ?? $this->name ?? $this->url;
        return "[$name]({$this->url})";
    }
}