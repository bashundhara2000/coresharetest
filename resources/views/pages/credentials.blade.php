<?php

$client = new Google_Client();
$client->setAuthConfigFile(__DIR__.'/client_id.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/drive/handle/oauth');
$client->addScope(Google_Service_Drive::DRIVE); //::DRIVE_METADATA_READONLY

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $access_token = $client->getAccessToken();
  file_put_contents(__DIR__."/credentials.json", json_encode($access_token));

  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

?>
