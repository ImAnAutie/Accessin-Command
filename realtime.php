<?php

	require __DIR__ . '/vendor/autoload.php';
	require_once 'unlockfunctions.php';
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
	$client->addTransportProvider(new PawlTransportProvider("ws://127.0.0.1:3003/ws"));
	$client->on('open', function (ClientSession $session) {
		global $functionsession;
		$functionsession=$session;

		$pinunlock = function ($args) {
			global $functionsession;
			$pin=$args[0];
			$doorid=$args[1];

	                $door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i",$doorid);

			//should check for same org as door
			$person = DB::queryFirstRow("SELECT * FROM people WHERE pin=%i", $pin);
			//If person found. (aka pin valid)
			if ($person) {
				echo "Found:".$person['name'];

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
					$functionsession->call('controller_'.$door['controller'].'_chirp', [true]);
				};
				return false;
			};
		};

		$session->register('accessincommand.unlock.pin', $pinunlock);


		$fobunlock = function ($args) {
			global $functionsession;
			$fob=$args[0];
			$doorid=$args[1];

	                $door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i",$doorid);

			//should check for same org as door
			$person = DB::queryFirstRow("SELECT * FROM people WHERE fob=%s", $fob);
			//If person found. (aka fob valid)
			if ($person) {
				echo "Found:".$person['name'];

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
					$functionsession->call('controller_'.$door['controller'].'_chirp', [true]);
				};
				return false;
			};
		};

		$session->register('accessincommand.unlock.fob', $fobunlock);


		$controllerping = function ($args) {
			$controllerid=$args[0];
			//check if valid, might eventualy pass along the jwt
			DB::update('controllers', array(
				'lastactive' => time()
			), "id=%i", $controllerid);
			return true;
		};

		$session->register('accessincommand.ping.controller', $controllerping);
	});

	$client->start();
?>
