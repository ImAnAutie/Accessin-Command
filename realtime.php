<?php

	require __DIR__ . '/vendor/autoload.php';
	require_once 'unlockfunctions.php';

	//so we can do json web tokens
	use \Firebase\JWT\JWT;

	require_once 'config.php';

	if (php_sapi_name() != "cli") {
		header("Location: /");
                die();
	};


	use Thruway\ClientSession;
	use Thruway\Peer\Client;
	use Thruway\Transport\PawlTransportProvider;

	$functionsession;

	$client = new Client("realm1");
	$client->addTransportProvider(new PawlTransportProvider("ws://127.0.0.1:3004/ws"));
	$client->on('open', function (ClientSession $session) {
		global $jwt_public_key, $jwt_private_key, $functionsession;
		$functionsession=$session;

		$pinunlock = function ($args) {
			global $jwt_public_key, $jwt_private_key, $functionsession;
			$pin=$args[0];
			$accesstoken=$args[1];

		        $decodedaccesstoken = (array) JWT::decode($accesstoken, $jwt_public_key, array('RS256'));
       		        $token=DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i",$decodedaccesstoken['jti']);
  	                $reader=DB::queryFirstRow("SELECT * FROM readers WHERE id=%i",$token['connectingobjectid']);

			$doorid=$reader['door'];
	                $door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i",$doorid);

			//should check for same org as door
			$person = DB::queryFirstRow("SELECT * FROM people WHERE pin=%i", $pin);
			//If person found. (aka pin valid)
			if ($person) {
				echo "Found:".$person['name'];
				if ($person['status']!=1) {
					echo "Person status not active. Access not granted.";
					return false;
				};

				DB::update('doors', array(
					'invalidattempt' => 0
				), "id=%i", $doorid);

			        return unlockdoor($doorid);
			} else {
				echo "Invalid PIN";

				$invalidcount=$door['invalidattempt']+1;
				DB::update('doors', array(
					'invalidattempt' => $invalidcount
				), "id=%i", $doorid);
				if ($invalidcount>=3) {
					echo "Alarm chirp";
					$functionsession->call('controller.'.$door['controller'].'.chirp', [true]);
				};
				return false;
			};
		};

		$session->register('accessincommand.unlock.pin', $pinunlock);

		$fobunlock = function ($args) {
			global $jwt_public_key, $jwt_private_key, $functionsession;
			$fob=$args[0];
			$accesstoken=$args[1];

		        $decodedaccesstoken = (array) JWT::decode($accesstoken, $jwt_public_key, array('RS256'));
       		        $token=DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i",$decodedaccesstoken['jti']);
  	                $reader=DB::queryFirstRow("SELECT * FROM readers WHERE id=%i",$token['connectingobjectid']);

			$doorid=$reader['door'];
	                $door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i",$doorid);

			//should check for same org as door
			$person = DB::queryFirstRow("SELECT * FROM people WHERE fob=%i", $fob);
			//If person found. (aka FOB valid)
			if ($person) {
				echo "Found:".$person['name'];
				if ($person['status']!=1) {
					echo "Person status not active. Access not granted.";
					return false;
				};

				DB::update('doors', array(
					'invalidattempt' => 0
				), "id=%i", $doorid);

			        return unlockdoor($doorid);
			} else {
				echo "Invalid fob";

				$invalidcount=$door['invalidattempt']+1;
				DB::update('doors', array(
					'invalidattempt' => $invalidcount
				), "id=%i", $doorid);
				if ($invalidcount>=3) {
					echo "Alarm chirp";
					$functionsession->call('controller.'.$door['controller'].'.chirp', [true]);
				};
				return false;
			};
		};

		$session->register('accessincommand.unlock.fob', $fobunlock);


		$controllerping = function ($args) {
			$controllerid=$args[0];
			//check if valid, might eventualy pass along the jwt
			DB::update('controllers', array(
				'lastactive' => time(),
				'setup' => true
			), "id=%i", $controllerid);
			return true;
		};

		$session->register('accessincommand.ping.controller', $controllerping);
	});

	$client->start();
?>
