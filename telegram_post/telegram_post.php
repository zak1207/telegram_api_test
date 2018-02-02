<?php

error_reporting(E_ALL);
date_default_timezone_set('Asia/Novokuznetsk');
define('TELEGRAM_BOT_TOKEN', '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11'); // Ваш токен
define('TELEGRAM_API_ENDPOINT', 'https://api.telegram.org/bot'.TELEGRAM_BOT_TOKEN.'/');
define('SCRIPT_BASE_DIRECTORY', './');
define('LOGS_DIRECTORY', SCRIPT_BASE_DIRECTORY.'/logs');

$file_name = 'mountain_paradise.jpg';	// Отправляемый файл
$chat_id = '-123456789';				// Идентификатор чат-группы или приватного чата
$text = 'Хорошего настроения :)';		// Текстовое сообщение

$send_response = tgApi_sendMessage($chat_id, $text);
var_dump($send_response);

$url = TELEGRAM_API_ENDPOINT.'sendPhoto?chat_id='.$chat_id;
$upload_photo_response = tgApi_photo_upload($url, 'mountain_paradise.jpg', 'photo' , 'Good wallpaper');
var_dump($upload_photo_response);


function tgApi_sendMessage($chat_id, $text) {
  return _tgApi_call('sendMessage', array(
    'chat_id' => $chat_id,
	'text' => $text,
  ));
}


function _tgApi_call($method, $params = array()) {
  $query = http_build_query($params);
  $url = TELEGRAM_API_ENDPOINT.$method.'?'.$query;

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    log_error($error);
    throw new Exception("Failed {$method} request");
  }

  curl_close($curl);

  $response = json_decode($json, true);
  if (!$response || !isset($response['ok'])) {
    log_error($json);
    throw new Exception("Invalid response for {$method} request");
  }

  return $response['ok'];
}


function tgApi_photo_upload($url, $file_name, $type, $caption) {
  if (!file_exists($file_name)) {
    throw new Exception('File not found: '.$file_name);
  }

  $post_fields = array ('caption' => $caption,
		$type => new CURLfile($file_name)
  );

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    log_error($error);
    throw new Exception("Failed {$url} request");
  }

  curl_close($curl);

  $response = json_decode($json, true);
  if (!$response) {
    throw new Exception("Invalid response for {$url} request");
  }

  return $response;
}


function log_error($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }

  _log_write('[ERROR] ' . $message);
}


function _log_write($message) {
  $trace = debug_backtrace();
  $function_name = isset($trace[2]) ? $trace[2]['function'] : '-';
  $mark = date("H:i:s") . ' [' . $function_name . ']';
  $log_name = LOGS_DIRECTORY.'/log_' . date("j.n.Y") . '.txt';
  file_put_contents($log_name, $mark . " : " . $message . "\n", FILE_APPEND);
}

?>
