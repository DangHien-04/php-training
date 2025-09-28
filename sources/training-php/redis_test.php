<?php
require __DIR__ . '/vendor/autoload.php';

use Predis\Client;

$host = 'redis-11642.c340.ap-northeast-2-1.ec2.redns.redis-cloud.com';
$port = 11642;
$username = 'default';
$password = 'toc6lnM0W4STNwIjI3n1ermzxUr3PVqg';

try {
	$client = new Client([
		'scheme' => 'tls',
		'host' => $host,
		'port' => $port,
		'username' => $username,
		'password' => $password,
	]);

	$client->set('foo', 'bar');
	$value = $client->get('foo');
	echo $value . PHP_EOL; // Expected: bar
	exit(0);
} catch (Exception $e) {
	fwrite(STDERR, 'Redis connection failed: ' . $e->getMessage() . PHP_EOL);
	exit(1);
}


