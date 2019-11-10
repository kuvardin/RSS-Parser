<?php

spl_autoload_register(static function (string $class_name) {
    $class_name = preg_replace('/^RssParser/', '', $class_name);
    $class_name = ltrim($class_name, '\\');
    require './src/' . str_replace('\\', '/', $class_name) . '.php';
});

$feeds = file('./test/rss_list.txt');
if ($feeds === false) {
    exit('Feeds list unavailable');
}

foreach ($feeds as $feed_url) {
    $feed_url = trim($feed_url);
    try {
        echo "$feed_url - ";
        $feed = RssParser\Parser::getFeed($feed_url, 3);
        echo "success\n";
    } catch (RssParser\Exceptions\CurlError $e) {
        echo "cURL error #{$e->getCode()}: {$e->getMessage()}\n";
    } catch (RssParser\Exceptions\HttpError $e) {
        echo "HTTP error #{$e->getCode()}\n";
    } catch (RssParser\Exceptions\UnknownField $e) {
        echo "{$e->getMessage()}\n";
    } catch (RssParser\Exceptions\Error $e) {
        if ($e->getMessage() === RssParser\Exceptions\Error::IS_NOT_XML) {
            echo "Not XML\n";
        } elseif ($e->getMessage() === RssParser\Exceptions\Error::XML_PARSING_ERROR) {
            echo "Parsing error\n";
        } else {
            echo $e;
        }
    }
}