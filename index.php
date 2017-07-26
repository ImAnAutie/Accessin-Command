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


$app->get('/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);
	$smarty->display('index.tpl');
});

$app->get('/signin[/{organisation}]', function (Request $request, Response $response, $args) {
	global $smarty;

	if ($_SESSION['accessin']['person']) {
		header("Location: /");
		die();
	};

	if ($args['organisation']) {
		$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE organisation=%s",$args['organisation']);
		if (!$organisation) {
			$smarty->display('invalid_organisation.tpl');
			die();
		} else {
			$smarty->assign('organisation',$organisation);
		};
	};
	$smarty->display('signin.tpl');
});

$app->post('/signin', function (Request $request, Response $response, $args) {
	global $smarty;

	if ($_SESSION['accessin']['person']) {
		header("Location: /");
		die();
	};

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE organisation=%s",$_POST['organisation']);
	if (!$organisation) {
		$smarty->display('invalid_organisation.tpl');
		die();
	} else {
		$person=DB::queryFirstRow("SELECT * FROM people WHERE organisation=%i",$organisation['id']);
		if (!$person) {
			$smarty->display('signin_invalid.tpl');
			die();
		} else {
			if (password_verify($_POST['password'], $person['password'])) {
				unset($person['password']);
				unset($_SESSION['accessin']);
				$_SESSION['accessin']['person']=$person;
				$_SESSION['accessin']['organisation']=$organisation['id'];
				echo "OK";
			} else {
				$smarty->display('signin_invalid.tpl');
				die();
			};
		};
	};
});

$app->get('/signout', function (Request $request, Response $response, $args) {
	unset($_SESSION['accessin']);
	header("Location: /");
	die();
});

$app->run();
?>
