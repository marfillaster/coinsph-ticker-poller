#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
// create a log channel
date_default_timezone_set("UTC");
$stdOut = new StreamHandler('php://stdout', Logger::INFO);
$stdOut->setFormatter(new LineFormatter(
    "%message%\n"

));

$log = new Logger('coinsph');
$log->pushHandler($stdOut);
$log->pushHandler(new StreamHandler('php://stderr', Logger::ERROR));


$process = new Process(<<<EOF
curl "https://quote.coins.ph/v1/markets/BTC-PHP" \
-H "Origin: https://coins.ph" \
-H "Accept-Encoding: gzip, deflate, sdch" \
-H "Accept-Language: en-US,en;q=0.8,fil;q=0.6" \
-H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36" \
-H "Accept: */*" \
-H "Cache-Control: max-age=0" \
-H "Referer: https://coins.ph/wallet" \
-H "Connection: keep-alive" \
-H "DNT: 1" \
--compressed --fail
EOF
);

while(true) {
  $expires = 30;
  $elapse = 0;
  $requestedAt = microtime(true);

  try {
      $process->mustRun();
      $response = $process->getOutput();
      $elapse =  microtime(true) - $requestedAt;

      if (null !== $payload = json_decode($response, true)  ) {


          if (isset($payload['market']['expires_in_seconds'])) {
            $expires = $payload['market']['expires_in_seconds'];
          }
      }

      $log->addInfo(preg_replace('/^\[|\]$/', '', json_encode(array(
          date("Y-m-d\TH:i:s").".000Z",
          'BTC-PHP',
          $payload['market']['ask'],
          $payload['market']['bid'],
      ))), array('elapse' => $elapse));
  } catch (ProcessFailedException $e) {
      $elapse =  microtime(true) - $requestedAt;
      $log->addError($process->getErrorOutput());
  }


  $wait = $expires - max(0, floor($elapse) - 2);
  if ($wait > 0) {

    sleep($wait);
  }
}
