<?php
session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
//so we can do json web tokens
use \Firebase\JWT\JWT;

require_once 'vendor/autoload.php';
require_once 'config.php';

//just a rough get it working thing here. Will actually do it properly in production.
$app->get('/mobile/doors', function (Request $request, Response $response) {
	$doors=DB::query("SELECT * FROM doors");
	$data['status']="true";
	$data['doors']=$doors;
	echo json_encode($data);
});

$app->get('/people/{id}', function (Request $request, Response $response,$args) {
	$me=DB::queryFirstRow("SELECT * FROM people WHERE id=%i",$args['id']);
	if (!$me) {
		$data['status']="false";
		$data['reason']="Invalid person id";
	} else {
		$data['status']="true";
		$data['name']=$me['name'];
		$data['status']=DB::queryFirstField("SELECT name FROM status WHERE id=%i",$me['status']);
		$myrights=DB::queryFirstRow("SELECT * FROM peoplerights WHERE person=%i",$args['id']);
		$data['myrights']=$myrights;
	};
	echo json_encode($data);
});

$app->run();
?>
