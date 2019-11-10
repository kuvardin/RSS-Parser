# RSS-Parser
PHP library for parse RSS-feeds

# Installing
```
composer require kuvardin/rss-parser
```

# Usage example
```
$url = 'http://site.com/rss.xml';
$feed = RssParser\Parser::getFeed($url);
print_r($feed);
```