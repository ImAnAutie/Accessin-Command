<?php
session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
//so we can do json web tokens
use \Firebase\JWT\JWT;

require_once 'vendor/autoload.php';
require_once 'config.php';

//just a rough get it working thing here. Will actually do it properly in production.
$app->get('/mobile/doors/', function (Request $request, Response $response) {
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

$app->get('/signin/[{organisation}/]', function (Request $request, Response $response, $args) {
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

$app->post('/signin/', function (Request $request, Response $response, $args) {
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
		$person=DB::queryFirstRow("SELECT * FROM people WHERE email=%s AND organisation=%i",$_POST['email'],$organisation['id']);
		if (!$person) {
			$smarty->display('signin_invalid.tpl');
			die();
		} else {
			if (password_verify($_POST['password'], $person['password'])) {
				unset($person['password']);
				unset($_SESSION['accessin']);
				$_SESSION['accessin']['person']=$person;
				$_SESSION['accessin']['organisation']=$organisation['id'];
				header("Location: /");
				die();
			} else {
				$smarty->display('signin_invalid.tpl');
				die();
			};
		};
	};
});

$app->get('/signout/', function (Request $request, Response $response, $args) {
	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	unset($_SESSION['accessin']);
	header("Location: /signin/".$organisation['organisation']);
	die();
});



//mobile api
$app->post('/mobileapi/signin/', function (Request $request, Response $response, $args) {
	global $smarty, $jwt_private_key;

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE organisation=%s",$_POST['organisation']);
	if (!$organisation) {
		$result['status']=false;
		$result['reason']="INVALID_ORGANISATION";
		echo json_encode($result);
		die();
	} else {
		$person=DB::queryFirstRow("SELECT * FROM people WHERE email=%s AND organisation=%i",$_POST['email'],$organisation['id']);
		if (!$person) {
			$result['status']=false;
			$result['reason']="INVALID_CREDENTIALS";
			echo json_encode($result);
			die();
		} else {
			if (password_verify($_POST['password'], $person['password'])) {
				$result['status']=true;

				DB::insert('mobiledevices', array(
					'person' => $person['id'],
					'lastactive' => time(),
					'organisation' => $organisation['id']
				));
				$mobileid=DB::insertId();

				$issuedat=time();
				//api 1 is Mobile
				//create accesstoken
				DB::insert('tokens', array(
					'api' => '1',
					'connectingobjectid' => $mobileid,
					'refreshtoken' => false,
					'issuedat' => $issuedat,
					'organisation' => $organisation['id']
				));
				$accesstokenid=DB::insertId();

				//create refreshtoken
				DB::insert('tokens', array(
					'api' => '1',
					'connectingobjectid' => $mobileid,
					'refreshtoken' => true,
					'refreshfor' => $accesstokenid,
					'issuedat' => $issuedat,
					'organisation' => $organisation['id']
				));
				$refreshtokenid=DB::insertId();

				DB::update('mobiledevices', array(
					refreshtokenid => $refreshtokenid,
					accesstokenid => $accesstokenid
				), "id=%i", $mobileid);

				//expiry is in 6 hours, if this is expired both door and API access cannot occur.
				$accesstoken = array(
					"iss" => "accessin.okonetwork.org.uk",
					"aud" => "AccessinCommandMobile",
					"jti" => $accesstokenid,
					"iat" => $issuedat,
					"exp" => $issuedat+21600
				);
				$result['accesstoken'] = JWT::encode($accesstoken, $jwt_private_key, 'RS256');

				//expires in 14 days. If this expires the mobile app will be forced to sign in again.
				$refreshtoken = array(
					"iss" => "accessin.okonetwork.org.uk",
					"aud" => "AccessinCommandMobile",
					"jti" => $refreshtokenid,
					"iat" => $issuedat,
					"exp" => $issuedat+1209600
				);
				$result['refreshtoken'] = JWT::encode($refreshtoken, $jwt_private_key, 'RS256');

				echo json_encode($result);
				die();
			} else {
				$result['status']=false;
				$result['reason']="INVALID_CREDENTIALS";
				echo json_encode($result);
				die();
			};
		};
	};
});





$app->run();
?>
