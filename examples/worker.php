<?php

require "../autoload.php";

use PhpAmqpLib\Connection\AMQPConnection;
use TaskMQ;

$conn = new AMQPConnection("localhost", 5672, "guest", "guest");
$worker = new TaskMQ\Worker(array( "conn" => $conn ));

$worker->bind("run_test", function() {
	echo("test message\n");
});

$worker->work();
