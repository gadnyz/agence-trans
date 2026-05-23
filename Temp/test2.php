<?php
require 'public/index.php';
$client = new \App\Libraries\ApiClient();
$res = $client->post('auth/login', ['username' => 'superadmin', 'password' => 'Kashala@202']);
if (isset($res['data']['access_token'])) {
    session()->set('access_token', $res['data']['access_token']);
}
$busRes = $client->get('bus');
var_dump($busRes);
