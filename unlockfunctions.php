<?php
session_start();

require_once 'vendor/autoload.php';
require_once 'config.php';
//anti csrf (https://github.com/BKcore/NoCSRF)
require_once 'nocsrf.php';


function unlockdoor($doorid) {
		$door=DB::queryFirstRow("SELECT * FROM doors WHERE id=%i",$doorid);
		$doorargs['door']=$door;
		$data['procedure']="controller.".$door['controller'].".unlock";
		$data['args']=$doorargs;
		$data=json_encode($data);

        	$headers = array('Content-Type' => 'application/json');
        	$httpget = Requests::post('http://127.0.0.1:3004/call', $headers, $data);

		$httpget = json_decode($httpget->body,true)['args'][0];
		return $httpget;
};
