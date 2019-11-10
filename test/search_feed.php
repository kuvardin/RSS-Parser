<?php

spl_autoload_register(static function (string $class_name) {
    $class_name = preg_replace('/^RssParser/', '', $class_name);
    $class_name = ltrim($class_name, '\\');
    require './src/' . str_replace('\\', '/', $class_name) . '.php';
});

$dirs = [
    '',
    '/api',
    '/category/poslednie-novosti/feed',
    '/data/rss',
    '/doc-list/rss',
    '/engine',
    '/export',
    '/export/rss',
    '/export/rss2/archive',
    '/feed',
    '/modules/news/cache',
    '/news/rss',
    '/newsline',
    '/out',
    '/rss',
    '/rss/all',
    '/rss/best',
    '/rss/category',
    '/rss/news',
    '/ru',
    '/ru/feed',
    '/ru/main',
    '/ru/news',
    '/ru/rss',
    '/rus',
    '/rus/rss/',
    '/russian',
    '/self',
    '/static',
    '/xml',
];

$resources = [
    '',
    '?feed=rss',
    '?feed=rss2',
    '?format=feed&type=rss',
    '?view=featured&format=feed&type=rss',
    'all',
    'all.xml',
    'articles',
    'articles-rss2.xml',
    'articles.rss',
    'blogs',
    'common',
    'feed',
    'feed.rss',
    'index.php?format=feed&type=rss',
    'index.xml',
    'itemlist?format=feed&type=rss',
    'newrss.xml',
    'news',
    'news.rss',
    'news.xml',
    'public-all.xml',
    'redtram.xml',
    'rss',
    'rss.asp',
    'rss.html',
    'rss.php',
    'rss.xml',
    'rss_news_rus.php',
    'rss_news_russian.xml',
    'rus.xml',
    'yandex.rss',
    'yandex.xml',
    'yandex.php',
    'yandex',
    'yandex.asp',
];

$domain = $argv[1] ?? null;
if (empty($domain)) {
    exit("Use: php search_feed.php %domain%\n");
}

foreach ($dirs as $dir) {
    foreach ($resources as $resource) {
        $url = $domain . $dir . '/' . $resource;

        while (true) {
            echo "$url - ";
            try {
                $feed = RssParser\Parser::getFeed($url);
                exit("success\n");
            } catch (RssParser\Exceptions\HttpError $e) {
                echo "HTTP error #{$e->getCode()}\n";
            } catch (RssParser\Exceptions\CurlError $e) {
                if ($e->getCode() === 28) {
                    echo "timeout\n";
                    continue;
                } else {
                    echo "cURL error #{$e->getCode()}: {$e->getMessage()}\n";
                }
            } catch (RssParser\Exceptions\Error $e) {
                if ($e->getMessage() === RssParser\Exceptions\Error::IS_NOT_XML) {
                    echo "XML parsing error\n";
                } else {
                    throw $e;
                }
            } catch (RssParser\Exceptions\UnknownField $e) {
                echo "{$e->getMessage()}\n";
            }
            break;
        }
    }
}