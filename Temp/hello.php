<?php
header('Content-Type: text/plain');
echo "hello\n";
echo "mysqli=" . (extension_loaded('mysqli') ? 'yes' : 'no') . "\n";
echo "pwd=" . (function_exists('password_verify') ? 'yes' : 'no') . "\n";
