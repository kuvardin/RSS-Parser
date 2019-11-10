<?php

namespace RssParser;

use RssParser\Exceptions\CurlError;
use RssParser\Exceptions\Error;
use RssParser\Exceptions\HttpError;

/**
 * Class Parser
 *
 * @package RssParser
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Parser
{
    public const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36';
    public const CONNECT_TIMEOUT = 7;
    public const TIMEOUT = 10;

    /**
     * @param string $url
     * @param int|null $attempts
     * @param array $curl_opts
     * @return Feed
     * @throws CurlError
     * @throws Error
     * @throws Exceptions\UnknownField
     * @throws HttpError
     */
    public static function getFeed(string $url, int $attempts = null, array $curl_opts = []): Feed
    {
        if ($attempts === null) {
            $attempts = 1;
        } elseif ($attempts < 1) {
            throw new Error('Attempts number must be greater than zero');
        }

        $ch = curl_init($url);
        $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
                CURLOPT_TIMEOUT => self::TIMEOUT,
                CURLOPT_USERAGENT => self::USER_AGENT,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => [
                    'DNT: 1',
                    'Upgrade-Insecure-Requests: 1',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                    'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Content-type: text/html; charset=UTF-8',
                ],

                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ] + $curl_opts;

        curl_setopt_array($ch, $options);

        $response = false;
        for ($i = 0; $i < $attempts && !$response; $i++) {
            $response = curl_exec($ch);
        }

        if ($response === false) {
            throw CurlError::make($ch);
        }

        $curl_info = curl_getinfo($ch);
        if ($curl_info['http_code'] !== 200) {
            throw new HttpError($curl_info['http_code']);
        }

        return Feed::make($url, $response, $curl_info);
    }

    /**
     * @param string $string
     * @return string|null
     */
    public static function filterString(string $string): ?string
    {
        $string = htmlspecialchars_decode($string);
        $string = htmlspecialchars_decode($string, ENT_QUOTES);
        $string = html_entity_decode($string, ENT_NOQUOTES, 'UTF-8');
        $string = trim(preg_replace('/([    ⁠]+)/u', ' ', $string));
        return $string === '' ? null : $string;
    }

    /**
     * @param string $int
     * @param bool $zero_to_null
     * @return int|null
     */
    public static function filterInt(string $int, bool $zero_to_null = false): ?int
    {
        if (!preg_match('/^\d+$/', $int)) {
            return null;
        }

        $result = (int)$int;

        if ($zero_to_null) {
            return $result === 0 ? null : $result;
        }

        return $result;
    }
}