<?php

require "../autoload.php";

use PhpAmqpLib\Connection\AMQPConnection;
use TaskMQ;

$conn = new AMQPConnection("localhost", 5672, "guest", "guest");
$client = new TaskMQ\Client(array( "conn" => $conn ));

$task = new TaskMQ\Task("run_test", "test message");
$client->submitTask($task);
