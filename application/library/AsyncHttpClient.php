<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/5/24
 * Time: 上午11:06
 */


class AsyncHttpClient {

  const DEFAULT_HEADERS = [
    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Charset' => 'GB2312,utf-8;q=0.7,*;q=0.7',
    'Accept-Encoding' => 'gzip,deflate',
    'User-Agent' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; zh-CN; rv:1.9.2) Gecko/20100115 Firefox/3.6',
  ];

  public function __construct() {
  }

  public static function httpGet($params, $callbackFun) {
    if(empty($params['host'])) call_user_func($callbackFun, null, null);
    Swoole\Async::dnsLookup($params['host'], function($domainName, $ip) use ($params, $callbackFun) {
      $headers = array_merge(['Host' => $domainName], self::DEFAULT_HEADERS);
      $timeout = isset($params['timeout']) ? $params['timeout'] : 20;
      $uri = !empty($params['path']) ? $params['path'] : '/';
      $ssl = $params['ssl'] ? true : false;
      $port = 80;
      if($ssl) $port = 443;
      if(!empty($params['port'])) $port = intval($params['port']);
      if(!empty($params['header']) && is_array($params['header'])) $headers = array_merge($headers, $params['header']);
      $cli = new swoole_http_client($ip, $port, $ssl);
      $cli->set(['timeout' => $timeout]);
      $cli->set(['keep_alive' => true]);
      $cli->setHeaders($headers);
      $cli->get($uri, function($cli) use ($callbackFun) {
        call_user_func($callbackFun, $cli->body, $cli->statusCode);
        $cli->close();
      });
    });
  }

  public static function httpPost($params, $callbackFun) {
    if(empty($params['host'])) call_user_func($callbackFun, null, null);
    Swoole\Async::dnsLookup($params['host'], function($domainName, $ip) use ($params, $callbackFun) {
      $headers = array_merge(['Host' => $domainName], self::DEFAULT_HEADERS);
      $timeout = isset($params['timeout']) ? $params['timeout'] : 20;
      $uri = !empty($params['path']) ? $params['path'] : '/';
      $data = !empty($params['data']) ? $params['data'] : [];
      $ssl = $params['ssl'] ? true : false;
      $port = 80;
      if($ssl) $port = 443;
      if(!empty($params['port'])) $port = intval($params['port']);
      if(!empty($params['header']) && is_array($params['header'])) $headers = array_merge($headers, $params['header']);
      $cli = new swoole_http_client($ip, $port, $ssl);
      $cli->set(['timeout' => $timeout]);
      $cli->setHeaders($headers);
      $cli->post($uri, $data, function($cli) use ($callbackFun) {
        call_user_func($callbackFun, $cli->body, $cli->statusCode);
        $cli->close();
      });
    });
  }


}