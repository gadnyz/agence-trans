<?php
$db = new mysqli('localhost', 'root', '', 'kashala_trans');
if ($db->connect_error) { die('Connect error: ' . $db->connect_error); }
var_dump($db->query('SELECT COUNT(*) FROM bus')->fetch_row());
var_dump($db->query('SELECT * FROM bus LIMIT 1')->fetch_assoc());
