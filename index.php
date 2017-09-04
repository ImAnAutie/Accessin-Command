<?php
session_start();
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
//so we can do json web tokens
use \Firebase\JWT\JWT;
require_once 'vendor/autoload.php';
require_once 'config.php';
//anti csrf (https://github.com/BKcore/NoCSRF)
require_once 'nocsrf.php';



require_once 'unlockfunctions.php';


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

	$leftsidebar['dashboard']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('index.tpl');
});


$app->get('/buildings/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a building

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$buildings=DB::query("SELECT * FROM buildings WHERE organisation=%i",$_SESSION['accessin']['organisation']);

	foreach ($buildings as &$building) {
		$building['country_name']=Iso3166\Codes::country($building['country_code']);
	};

	$smarty->assign('buildings',$buildings);

	$leftsidebar['buildings']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('buildings.tpl');
});

$app->get('/buildings/add/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$leftsidebar['buildings']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('buildings_add.tpl');
});

$app->post('/buildings/add/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a building


	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
		DB::insert('buildings', array(
			'name' => $_POST['name'],
			'line1' => $_POST['line1'],
			'line2' => $_POST['line2'],
			'city' => $_POST['city'],
			'postcode' => $_POST['postcode'],
			'country_code' => $_POST['country_code'],
			'organisation' => $_SESSION['accessin']['organisation']
		));
		header("Location: /buildings");
		die();
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->post('/buildings/{buildingid}/delete/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to delete a building
	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );

		$building=DB::queryFirstRow("SELECT * FROM buildings WHERE id=%i AND organisation=%i",$args['buildingid'],$_SESSION['accessin']['organisation']);
		if (!$building) {
			$smarty->display('invalid_building.tpl');
			die();
		} else {
			DB::delete('buildings', "id=%i AND organisation=%i", $args['buildingid'],$_SESSION['accessin']['organisation']);
			header("Location: /buildings");
			die();
		};
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->get('/buildings/{buildingid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a building
	$building=DB::queryFirstRow("SELECT * FROM buildings WHERE id=%i AND organisation=%i",$args['buildingid'],$_SESSION['accessin']['organisation']);
	if (!$building) {
		$smarty->display('invalid_building.tpl');
		die();
	} else {
		$building['coun#try_name']=Iso3166\Codes::country($building['country_code']);
		$smarty->assign('building',$building);

		$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
		$smarty->assign('session',$_SESSION['accessin']);
		$smarty->assign('organisation',$organisation);

		$leftsidebar['buildings']=true;
		$smarty->assign('leftsidebar',$leftsidebar);

		$csrftoken = NoCSRF::generate('csrf_token');
		$smarty->assign('csrftoken',$csrftoken);

		$smarty->display('buildings_edit.tpl');
		die();
	};
});



$app->post('/buildings/{buildingid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a building
	$building=DB::queryFirstRow("SELECT * FROM buildings WHERE id=%i AND organisation=%i",$args['buildingid'],$_SESSION['accessin']['organisation']);
	if (!$building) {
		$smarty->display('invalid_building.tpl');
		die();
	} else {
		try {
			NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
			DB::update('buildings', array(
				'name' => $_POST['name'],
				'line1' => $_POST['line1'],
				'line2' => $_POST['line2'],
				'city' => $_POST['city'],
				'postcode' => $_POST['postcode'],
				'country_code' => $_POST['country_code']
			), "id=%i AND organisation=%i", $args['buildingid'],$_SESSION['accessin']['organisation']);

			header("Location: /buildings");
			die();
		} catch ( Exception $e ) {
			$smarty->display('csrffail.tpl');
			die();
		};
	};
});

//***

$app->get('/controllers/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a controller

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$controllers=DB::query("SELECT * FROM controllers WHERE organisation=%i",$_SESSION['accessin']['organisation']);
	foreach ($controllers as &$controller) {
		$controller['building_name']=DB::queryFirstField("SELECT name FROM buildings WHERE id=%i AND organisation=%i",$controller['building'],$_SESSION['accessin']['organisation']);
	};
	$smarty->assign('controllers',$controllers);

	$leftsidebar['controllers']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('controllers.tpl');
});

$app->get('/controllers/add/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$buildings=DB::query("SELECT * FROM buildings WHERE organisation=%i",$_SESSION['accessin']['organisation']);
	foreach ($buildings as &$building) {
		$building['country_name']=Iso3166\Codes::country($building['country_code']);
	};
	$smarty->assign('buildings',$buildings);

	$leftsidebar['controllers']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('controllers_add.tpl');
});

$app->post('/controllers/add/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a controller

	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
		$building=DB::queryFirstRow("SELECT * FROM buildings WHERE id=%i AND organisation=%i",$_POST['building'],$_SESSION['accessin']['organisation']);
		if (!$building) {
			$smarty->display('invalid_building.tpl');
			die();
		} else {
			DB::insert('controllers', array(
				'name' => $_POST['name'],
				'building' => $_POST['building'],
				'organisation' => $_SESSION['accessin']['organisation']
			));
			header("Location: /controllers");
			die();
		};
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
 });

$app->post('/controllers/{controllerid}/delete/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to delete a controller
	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );

		$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$args['controllerid'],$_SESSION['accessin']['organisation']);
		if (!$controller) {
			$smarty->display('invalid_controller.tpl');
			die();
		} else {
			DB::delete('controllers', "id=%i AND organisation=%i", $args['controllerid'],$_SESSION['accessin']['organisation']);
			header("Location: /controllers");
			die();
		};
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->get('/controllers/{controllerid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a controller
	$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$args['controllerid'],$_SESSION['accessin']['organisation']);
	if (!$controller) {
		$smarty->display('invalid_controller.tpl');
		die();
	} else {
		$controller['country_name']=Iso3166\Codes::country($controller['country_code']);
		$smarty->assign('controller',$controller);

		$buildings=DB::query("SELECT * FROM buildings WHERE organisation=%i",$_SESSION['accessin']['organisation']);
		foreach ($buildings as &$building) {
			$building['country_name']=Iso3166\Codes::country($building['country_code']);
		};
		$smarty->assign('buildings',$buildings);

		$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
		$smarty->assign('session',$_SESSION['accessin']);
		$smarty->assign('organisation',$organisation);

		$leftsidebar['controllers']=true;
		$smarty->assign('leftsidebar',$leftsidebar);

		$csrftoken = NoCSRF::generate('csrf_token');
		$smarty->assign('csrftoken',$csrftoken);

		$smarty->display('controllers_edit.tpl');
		die();
	};
});

$app->post('/controllers/{controllerid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a controller
	$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$args['controllerid'],$_SESSION['accessin']['organisation']);
	if (!$controller) {
		$smarty->display('invalid_controller.tpl');
		die();
	} else {
		try {
			NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
			DB::update('controllers', array(
				'name' => $_POST['name'],
				'building' => $_POST['building']
			), "id=%i AND organisation=%i", $args['controllerid'],$_SESSION['accessin']['organisation']);

			header("Location: /controllers");
			die();
		} catch ( Exception $e ) {
			$smarty->display('csrffail.tpl');
			die();
		};
	};
});


$app->get('/controllers/{controllerid}/setup/', function (Request $request, Response $response, $args) {
	global $jwt_private_key, $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a controller
	$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$args['controllerid'],$_SESSION['accessin']['organisation']);
	if (!$controller) {
		$smarty->display('invalid_controller.tpl');
		die();
	} else {
		if ($controller['setup']) {
			$smarty->display('controller_allready_setup.tpl');
			die();
		} else {
			//remove any old tokens for controller
			DB::delete('tokens', "api=%i AND connectingobjectid=%i AND organisation=%i", 2,$args['controllerid'],$_SESSION['accessin']['organisation']);

			$issuedat=time();

			//create refreshtoken
			DB::insert('tokens', array(
				'api' => '2',
				'connectingobjectid' => $controller['id'],
				'refreshtoken' => true,
				'refreshfor' => 0,
				'issuedat' => $issuedat,
				'organisation' => $_SESSION['accessin']['organisation']
			));
			$refreshtokenid=DB::insertId();

			DB::update('controllers', array(
				refreshtokenid => $refreshtokenid,
			), "id=%i", $controller['id']);

			//expires in 60 days. If this expires then the controller will need to be reset.
			$refreshtoken = array(
				"iss" => "accessin.okonetwork.org.uk",
				"aud" => "AccessinCommandController",
				"jti" => $refreshtokenid,
				"iat" => $issuedat,
				"exp" => $issuedat+5184000
			);
			$controller['refreshtoken'] = JWT::encode($refreshtoken, $jwt_private_key, 'RS256');
			$smarty->assign('controller',$controller);

			$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
			$smarty->assign('session',$_SESSION['accessin']);
			$smarty->assign('organisation',$organisation);

			$leftsidebar['controllers']=true;
			$smarty->assign('leftsidebar',$leftsidebar);

			$csrftoken = NoCSRF::generate('csrf_token');
			$smarty->assign('csrftoken',$csrftoken);

			$smarty->display('controllers_setup.tpl');
			die();
		};
	};
});

/// ********************
$app->get('/readers/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a reader

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$readers=DB::query("SELECT * FROM readers WHERE organisation=%i",$_SESSION['accessin']['organisation']);
	foreach ($readers as &$reader) {
		$reader['door_name']=DB::queryFirstField("SELECT name FROM doors WHERE id=%i AND organisation=%i",$reader['door'],$_SESSION['accessin']['organisation']);
	};
	$smarty->assign('readers',$readers);

	$leftsidebar['readers']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('readers.tpl');
});

$app->get('/readers/add/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$doors=DB::query("SELECT * FROM doors WHERE organisation=%i",$_SESSION['accessin']['organisation']);
	foreach ($doors as &$door) {
		$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$door['controller'], $_SESSION['accessin']['organisation']);
		$door['building_name']=DB::queryFirstField("SELECT name FROM buildings WHERE id=%i AND organisation=%i",$controller['building'], $_SESSION['accessin']['organisation']);
	};
	$smarty->assign('doors',$doors);

	$leftsidebar['readers']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('readers_add.tpl');
});

$app->post('/readers/add/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a reader
	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
		$door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i AND organisation=%i",$_POST['door'],$_SESSION['accessin']['organisation']);
		if (!$door) {
			$smarty->display('invalid_door.tpl');
			die();
		} else {
			DB::insert('readers', array(
				'name' => $_POST['name'],
				'door' => $_POST['door'],
				'organisation' => $_SESSION['accessin']['organisation']
			));
			header("Location: /readers");
			die();
		};
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->post('/readers/{readerid}/delete/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to delete a reader
	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );

		$reader=DB::queryFirstRow("SELECT * FROM readers WHERE id=%i AND organisation=%i",$args['readerid'],$_SESSION['accessin']['organisation']);
		if (!$reader) {
			$smarty->display('invalid_reader.tpl');
			die();
		} else {
			DB::delete('readers', "id=%i AND organisation=%i", $args['readerid'],$_SESSION['accessin']['organisation']);
			header("Location: /readers");
			die();
		};
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->get('/readers/{readerid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a reader
	$reader=DB::queryFirstRow("SELECT * FROM readers WHERE id=%i AND organisation=%i",$args['readerid'],$_SESSION['accessin']['organisation']);
	if (!$reader) {
		$smarty->display('invalid_reader.tpl');
		die();
	} else {
		$smarty->assign('reader',$reader);

		$doors=DB::query("SELECT * FROM doors WHERE organisation=%i",$_SESSION['accessin']['organisation']);
		foreach ($doors as &$door) {
			$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$door['controller'], $_SESSION['accessin']['organisation']);
			$door['building_name']=DB::queryFirstField("SELECT name FROM buildings WHERE id=%i AND organisation=%i",$controller['building'], $_SESSION['accessin']['organisation']);
		};
		$smarty->assign('doors',$doors);

		$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
		$smarty->assign('session',$_SESSION['accessin']);
		$smarty->assign('organisation',$organisation);

		$leftsidebar['readers']=true;
		$smarty->assign('leftsidebar',$leftsidebar);

		$csrftoken = NoCSRF::generate('csrf_token');
		$smarty->assign('csrftoken',$csrftoken);

		$smarty->display('readers_edit.tpl');
		die();
	};
});

$app->post('/readers/{readerid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a reader
	$reader=DB::queryFirstRow("SELECT * FROM readers WHERE id=%i AND organisation=%i",$args['readerid'],$_SESSION['accessin']['organisation']);
	if (!$reader) {
		$smarty->display('invalid_reader.tpl');
		die();
	} else {
		try {
			NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
			DB::update('readers', array(
				'name' => $_POST['name'],
				'door' => $_POST['door']
			), "id=%i AND organisation=%i", $args['readerid'],$_SESSION['accessin']['organisation']);

			header("Location: /readers");
			die();
		} catch ( Exception $e ) {
			$smarty->display('csrffail.tpl');
			die();
		};
	};
});


$app->get('/readers/{readerid}/setup/', function (Request $request, Response $response, $args) {
	global $jwt_private_key, $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a reader
	$reader=DB::queryFirstRow("SELECT * FROM readers WHERE id=%i AND organisation=%i",$args['readerid'],$_SESSION['accessin']['organisation']);
	if (!$reader) {
		$smarty->display('invalid_reader.tpl');
		die();
	} else {
		if ($reader['setup']) {
			$smarty->display('reader_allready_setup.tpl');
			die();
		} else {
			//remove any old tokens for reader
			DB::delete('tokens', "api=%i AND connectingobjectid=%i AND organisation=%i", 3,$args['readerid'],$_SESSION['accessin']['organisation']);

			$issuedat=time();

			//create refreshtoken
			DB::insert('tokens', array(
				'api' => '3',
				'connectingobjectid' => $reader['id'],
				'refreshtoken' => true,
				'refreshfor' => 0,
				'issuedat' => $issuedat,
				'organisation' => $_SESSION['accessin']['organisation']
			));
			$refreshtokenid=DB::insertId();

			DB::update('readers', array(
				refreshtokenid => $refreshtokenid,
			), "id=%i", $reader['id']);

			//expires in 60 days. If this expires then the reader will need to be reset.
			$refreshtoken = array(
				"iss" => "accessin.okonetwork.org.uk",
				"aud" => "AccessinCommandreader",
				"jti" => $refreshtokenid,
				"iat" => $issuedat,
				"exp" => $issuedat+5184000
			);
			$reader['refreshtoken'] = JWT::encode($refreshtoken, $jwt_private_key, 'RS256');
			$smarty->assign('reader',$reader);

			$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
			$smarty->assign('session',$_SESSION['accessin']);
			$smarty->assign('organisation',$organisation);

			$leftsidebar['readers']=true;
			$smarty->assign('leftsidebar',$leftsidebar);

			$csrftoken = NoCSRF::generate('csrf_token');
			$smarty->assign('csrftoken',$csrftoken);

			$smarty->display('readers_setup.tpl');
			die();
		};
	};
});

/// ********************
/// ********************

$app->get('/doors/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a door

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$doors=DB::query("SELECT * FROM doors WHERE organisation=%i",$_SESSION['accessin']['organisation']);
	foreach ($doors as &$door) {
		$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$door['controller'],$_SESSION['accessin']['organisation']);
		$door['controller_name']=$controller['name'];
		$door['building']=$controller['building'];
		$door['building_name']=DB::queryFirstField("SELECT name FROM buildings WHERE id=%i AND organisation=%i",$door['building'],$_SESSION['accessin']['organisation']);
	};
	$smarty->assign('doors',$doors);

	$leftsidebar['doors']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('doors.tpl');
});

$app->get('/doors/add/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$controllers=DB::query("SELECT * FROM controllers WHERE organisation=%i",$_SESSION['accessin']['organisation']);
	foreach ($controllers as &$controller) {
		$controller['building_name']=DB::queryFirstField("SELECT name FROM buildings WHERE id=%i AND organisation=%i",$controller['building'],$_SESSION['accessin']['organisation']);
	};
	$smarty->assign('controllers',$controllers);

	$leftsidebar['doors']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('doors_add.tpl');
});

$app->post('/doors/add/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a door

	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
		$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$_POST['controller'],$_SESSION['accessin']['organisation']);
		if (!$controller) {
			$smarty->display('invalid_controller.tpl');
			die();
		} else {
			DB::insert('doors', array(
				'name' => $_POST['name'],
				'controller' => $_POST['controller'],
				'relay' => $_POST['relay'],
				'organisation' => $_SESSION['accessin']['organisation']
			));
			header("Location: /doors");
			die();
		};
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->post('/doors/{doorid}/delete/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to delete a door
	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );

		$door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i AND organisation=%i",$args['doorid'],$_SESSION['accessin']['organisation']);
		if (!$door) {
			$smarty->display('invalid_door.tpl');
			die();
		} else {
			DB::delete('doors', "id=%i AND organisation=%i", $args['doorid'],$_SESSION['accessin']['organisation']);
			header("Location: /doors");
			die();
		};
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->get('/doors/{doorid}/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a door
	$door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i AND organisation=%i",$args['doorid'],$_SESSION['accessin']['organisation']);
	if (!$door) {
		$smarty->display('invalid_door.tpl');
		die();
	} else {
		$smarty->assign('door',$door);

		$doorlogs=DB::query("SELECT * FROM doorlogs WHERE door=%i AND organisation=%i",$door['id'],$_SESSION['accessin']['organisation']);
		$smarty->assign('doorlogs',$doorlogs);

		$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$door['controller'],$_SESSION['accessin']['organisation']);
		$smarty->assign('controller',$controller);

		$building=DB::queryFirstRow("SELECT * FROM buildings WHERE id=%i AND organisation=%i",$controller['building'],$_SESSION['accessin']['organisation']);
		$smarty->assign('building',$building);

		$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
		$smarty->assign('session',$_SESSION['accessin']);
		$smarty->assign('organisation',$organisation);

		$leftsidebar['doors']=true;
		$smarty->assign('leftsidebar',$leftsidebar);

		$csrftoken = NoCSRF::generate('csrf_token');
		$smarty->assign('csrftoken',$csrftoken);

		$smarty->display('doors_door.tpl');
		die();
	};
});


$app->get('/doors/{doorid}/unlock/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	$result=unlockdoor($args['doorid']);
	echo json_encode($result);
});

$app->get('/doors/{doorid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a door
	$door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i AND organisation=%i",$args['doorid'],$_SESSION['accessin']['organisation']);
	if (!$door) {
		$smarty->display('invalid_door.tpl');
		die();
	} else {
		$door['country_name']=Iso3166\Codes::country($door['country_code']);
		$smarty->assign('door',$door);

		$controllers=DB::query("SELECT * FROM controllers WHERE organisation=%i",$_SESSION['accessin']['organisation']);
		foreach ($controllers as &$controller) {
			$controller['building_name']=DB::queryFirstField("SELECT name FROM buildings WHERE id=%i AND organisation=%i",$controller['building'],$_SESSION['accessin']['organisation']);
		};
		$smarty->assign('controllers',$controllers);

		$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
		$smarty->assign('session',$_SESSION['accessin']);
		$smarty->assign('organisation',$organisation);

		$leftsidebar['doors']=true;
		$smarty->assign('leftsidebar',$leftsidebar);

		$csrftoken = NoCSRF::generate('csrf_token');
		$smarty->assign('csrftoken',$csrftoken);

		$smarty->display('doors_edit.tpl');
		die();
	};
});

$app->post('/doors/{doorid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a door
	$door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i AND organisation=%i",$args['doorid'],$_SESSION['accessin']['organisation']);
	if (!$door) {
		$smarty->display('invalid_door.tpl');
		die();
	} else {
		$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i AND organisation=%i",$_POST['controller'],$_SESSION['accessin']['organisation']);
		if (!$controller) {
			$smarty->display('invalid_controller.tpl');
			die();
		} else {
			try {
				NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
				DB::update('doors', array(
					'name' => $_POST['name'],
					'controller' => $_POST['controller'],
					'relay' => $_POST['relay']
				), "id=%i AND organisation=%i", $args['doorid'],$_SESSION['accessin']['organisation']);
				header("Location: /doors");
				die();
			} catch ( Exception $e ) {
				$smarty->display('csrffail.tpl');
				die();
			};
		};
	};
});


//**start of people
$app->get('/people/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a person

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$people=DB::query("SELECT * FROM people WHERE organisation=%i",$_SESSION['accessin']['organisation']);
	foreach ($people as &$person) {
		//yes it is correct not to check for a organisation. The range of possible statues are the same for all orgs.
		$status=DB::queryFirstRow("SELECT * FROM status WHERE id=%i",$person['status']);
		$person['status_name']=$status['name'];
		$person['status_description']=$status['description'];
	};
	$smarty->assign('people',$people);

	$leftsidebar['people']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('people.tpl');
});

$app->get('/people/add/', function (Request $request, Response $response, $args) {
	global $smarty;
	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};

	$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
	$smarty->assign('session',$_SESSION['accessin']);
	$smarty->assign('organisation',$organisation);

	$leftsidebar['people']=true;
	$smarty->assign('leftsidebar',$leftsidebar);

	$csrftoken = NoCSRF::generate('csrf_token');
	$smarty->assign('csrftoken',$csrftoken);

	$smarty->display('people_add.tpl');
});

$app->post('/people/add/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to add a person
	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
		DB::insert('people', array(
			'name' => $_POST['name'],
			'email' => $_POST['email'],
			'status' => 4,
			'organisation' => $_SESSION['accessin']['organisation']
		));
		header("Location: /people");
		die();
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->post('/people/{personid}/delete/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to delete a person
	try {
		NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );

		$person=DB::queryFirstRow("SELECT * FROM people WHERE id=%i AND organisation=%i",$args['personid'],$_SESSION['accessin']['organisation']);
		if (!$person) {
			$smarty->display('invalid_person.tpl');
			die();
		} else {
			DB::delete('people', "id=%i AND organisation=%i", $args['personid'],$_SESSION['accessin']['organisation']);
			header("Location: /people");
			die();
		};
	} catch ( Exception $e ) {
		$smarty->display('csrffail.tpl');
		die();
	};
});

$app->get('/people/{personid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a person
	$person=DB::queryFirstRow("SELECT * FROM people WHERE id=%i AND organisation=%i",$args['personid'],$_SESSION['accessin']['organisation']);
	if (!$person) {
		$smarty->display('invalid_person.tpl');
		die();
	} else {
		$smarty->assign('person',$person);

		$organisation=DB::queryFirstRow("SELECT * FROM saasorganisations WHERE id=%i",$_SESSION['accessin']['organisation']);
		$smarty->assign('session',$_SESSION['accessin']);
		$smarty->assign('organisation',$organisation);

		$leftsidebar['people']=true;
		$smarty->assign('leftsidebar',$leftsidebar);

		$csrftoken = NoCSRF::generate('csrf_token');
		$smarty->assign('csrftoken',$csrftoken);

		$smarty->display('people_edit.tpl');
		die();
	};
});

$app->post('/people/{personid}/edit/', function (Request $request, Response $response, $args) {
	global $smarty;

	if (!$_SESSION['accessin']['person']) {
		header("Location: /signin");
		die();
	};
	//should also check here if user has permission to edit a person
	$person=DB::queryFirstRow("SELECT * FROM people WHERE id=%i AND organisation=%i",$args['personid'],$_SESSION['accessin']['organisation']);
	if (!$person) {
		$smarty->display('invalid_person.tpl');
		die();
	} else {
		try {
			NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
			DB::update('people', array(
				'name' => $_POST['name'],
				'email' => $_POST['email']
			), "id=%i AND organisation=%i", $args['personid'],$_SESSION['accessin']['organisation']);

			header("Location: /people");
			die();
		} catch ( Exception $e ) {
			$smarty->display('csrffail.tpl');
			die();
		};
	};
});

//**


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
	global $jwt_private_key;

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

$app->get('/mobileapi/refreshtoken/', function (Request $request, Response $response) {
	global $jwt_public_key, $jwt_private_key;

	$authheader=getallheaders()['authorization'];
	$refreshtoken=explode(' ', $authheader, 2)[1];

	$decodedrefreshtoken = (array) JWT::decode($refreshtoken, $jwt_public_key, array('RS256'));

	$result['status']=true;

	if ($decodedrefreshtoken['iss']!="accessin.okonetwork.org.uk"||$decodedrefreshtoken['aud']!="AccessinCommandMobile") {
		$result['status']=false;
		$result['reason']="INVALID_JWT";
	} else {
		$token=DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i",$decodedrefreshtoken['jti']);
		if (!$token) {
			$result['status']=false;
			$result['reason']="INVALID_JWT";
		} else {
			if ($token['blocked']) {
				$result['status']=false;
				$result['reason']="BLOCKED_JWT";
			} else {
				$mobile=DB::queryFirstRow("SELECT * FROM mobiledevices WHERE id=%i",$token['connectingobjectid']);
				if (!$mobile) {
					$result['status']=false;
					$result['reason']="BLOCKED_DEVICE";
				} else {
					if ($mobile['refreshtokenid']!=$token['id']) {
						$result['status']=false;
						$result['reason']="INVALID_JWT";
					} else {
						//block old jwt
						DB::update('tokens', array( 'blocked' => '1' ), "id=%i", $token['id']);
						DB::update('tokens', array( 'blocked' => '1' ), "id=%i", $token['refreshfor']);
						DB::update('mobiledevices', array( 'refreshtokenid' => '0','accesstokenid' => '0' ), "id=%i", $mobile['id']);

						//issue new jwt.

						$mobileid=$mobile['id'];
						$organisation=$token['organisation'];

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
						$result['status']=true;
					};
				};
			};
		};
	};

	echo json_encode($result);
});






$app->get('/controllerapi/refreshtoken/', function (Request $request, Response $response) {
	global $jwt_public_key, $jwt_private_key;

	$authheader=getallheaders()['authorization'];
	$refreshtoken=explode(' ', $authheader, 2)[1];

	$decodedrefreshtoken = (array) JWT::decode($refreshtoken, $jwt_public_key, array('RS256'));

	$result['status']=true;

	if ($decodedrefreshtoken['iss']!="accessin.okonetwork.org.uk"||$decodedrefreshtoken['aud']!="AccessinCommandController") {
		$result['status']=false;
		$result['reason']="INVALID_JWT";
	} else {
		$token=DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i",$decodedrefreshtoken['jti']);
		if (!$token) {
			$result['status']=false;
			$result['reason']="INVALID_JWT";
		} else {
			if ($token['blocked']) {
				$result['status']=false;
				$result['reason']="BLOCKED_JWT";
			} else {
				$controller=DB::queryFirstRow("SELECT * FROM controllers WHERE id=%i",$token['connectingobjectid']);
				if (!$controller) {
					$result['status']=false;
					$result['reason']="BLOCKED_DEVICE";
				} else {
					if ($controller['refreshtokenid']!=$token['id']) {
						$result['status']=false;
						$result['reason']="INVALID_JWT";
					} else {
						//block old jwt
						DB::update('tokens', array( 'blocked' => '1' ), "id=%i", $token['id']);
						DB::update('tokens', array( 'blocked' => '1' ), "id=%i", $token['refreshfor']);
						DB::update('controllers', array( 'refreshtokenid' => '0','accesstokenid' => '0' ), "id=%i", $controller['id']);

						//issue new jwt.

						$controllerid=$controller['id'];
						$organisation=$token['organisation'];

						$issuedat=time();
						//api 2 is Controller
						//create accesstoken
						DB::insert('tokens', array(
							'api' => '2',
							'connectingobjectid' => $controllerid,
							'refreshtoken' => false,
							'issuedat' => $issuedat,
							'organisation' => $organisation['id']
						));
						$accesstokenid=DB::insertId();

						//create refreshtoken
						DB::insert('tokens', array(
							'api' => '2',
							'connectingobjectid' => $controllerid,
							'refreshtoken' => true,
							'refreshfor' => $accesstokenid,
							'issuedat' => $issuedat,
							'organisation' => $organisation['id']
						));
						$refreshtokenid=DB::insertId();

						DB::update('controllers', array(
							refreshtokenid => $refreshtokenid,
							accesstokenid => $accesstokenid
						), "id=%i", $controllerid);

						//expiry is in 6 hours, if this is expired both door and API access cannot occur.
						$accesstoken = array(
							"iss" => "accessin.okonetwork.org.uk",
							"aud" => "AccessinCommandController",
							"jti" => $accesstokenid,
							"iat" => $issuedat,
							"exp" => $issuedat+21600
						);
						$result['accesstoken'] = JWT::encode($accesstoken, $jwt_private_key, 'RS256');

						//expires in 14 days. If this expires the mobile app will be forced to sign in again.
						$refreshtoken = array(
							"iss" => "accessin.okonetwork.org.uk",
							"aud" => "AccessinCommandController",
							"jti" => $refreshtokenid,
							"iat" => $issuedat,
							"exp" => $issuedat+1209600
						);
						$result['refreshtoken'] = JWT::encode($refreshtoken, $jwt_private_key, 'RS256');
						$result['status']=true;
					};
				};
			};
		};
	};

	echo json_encode($result);
});





$app->get('/readerapi/refreshtoken/', function (Request $request, Response $response) {
	global $jwt_public_key, $jwt_private_key;

	$authheader=getallheaders()['authorization'];
	$refreshtoken=explode(' ', $authheader, 2)[1];

	$decodedrefreshtoken = (array) JWT::decode($refreshtoken, $jwt_public_key, array('RS256'));

	$result['status']=true;

	if ($decodedrefreshtoken['iss']!="accessin.okonetwork.org.uk"||$decodedrefreshtoken['aud']!="AccessinCommandreader") {
		$result['status']=false;
		$result['reason']="INVALID_JWT";
	} else {
		$token=DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i",$decodedrefreshtoken['jti']);
		if (!$token) {
			$result['status']=false;
			$result['reason']="INVALID_JWT";
		} else {
			if ($token['blocked']) {
				$result['status']=false;
				$result['reason']="BLOCKED_JWT";
			} else {
				$reader=DB::queryFirstRow("SELECT * FROM readers WHERE id=%i",$token['connectingobjectid']);
				if (!$reader) {
					$result['status']=false;
					$result['reason']="BLOCKED_DEVICE";
				} else {
					if ($reader['refreshtokenid']!=$token['id']) {
						$result['status']=false;
						$result['reason']="INVALID_JWT";
					} else {
						//block old jwt
						DB::update('tokens', array( 'blocked' => '1' ), "id=%i", $token['id']);
						DB::update('tokens', array( 'blocked' => '1' ), "id=%i", $token['refreshfor']);
						DB::update('readers', array( 'refreshtokenid' => '0','accesstokenid' => '0' ), "id=%i", $reader['id']);

						//issue new jwt.

						$readerid=$reader['id'];
						$organisation=$token['organisation'];

						$issuedat=time();
						//api 3 is reader
						//create accesstoken
						DB::insert('tokens', array(
							'api' => '3',
							'connectingobjectid' => $readerid,
							'refreshtoken' => false,
							'issuedat' => $issuedat,
							'organisation' => $organisation['id']
						));
						$accesstokenid=DB::insertId();

						//create refreshtoken
						DB::insert('tokens', array(
							'api' => '3',
							'connectingobjectid' => $readerid,
							'refreshtoken' => true,
							'refreshfor' => $accesstokenid,
							'issuedat' => $issuedat,
							'organisation' => $organisation['id']
						));
						$refreshtokenid=DB::insertId();

						DB::update('readers', array(
							refreshtokenid => $refreshtokenid,
							accesstokenid => $accesstokenid
						), "id=%i", $readerid);

						//expiry is in 6 hours, if this is expired a refresh is required for continued operation.
						$accesstoken = array(
							"iss" => "accessin.okonetwork.org.uk",
							"aud" => "AccessinCommandreader",
							"jti" => $accesstokenid,
							"iat" => $issuedat,
							"exp" => $issuedat+21600
						);
						$result['accesstoken'] = JWT::encode($accesstoken, $jwt_private_key, 'RS256');

						//expires in 14 days. If this expires the reader will need to be setup again.
						$refreshtoken = array(
							"iss" => "accessin.okonetwork.org.uk",
							"aud" => "AccessinCommandreader",
							"jti" => $refreshtokenid,
							"iat" => $issuedat,
							"exp" => $issuedat+1209600
						);
						$result['refreshtoken'] = JWT::encode($refreshtoken, $jwt_private_key, 'RS256');
						$result['status']=true;
					};
				};
			};
		};
	};

	echo json_encode($result);
});




$app->run();
?>
