<?php

namespace TaskMQ;
use PhpAmqpLib\Connection\AMQPConnection;

class BaseConnection {
	/**
	 * Establish or reuse connection to RabbitMQ server
	 * 
	 * @param $args array Connection properties
	 */
	public function __construct($args) {
		$properties = array(
			"host" => "localhost",
			"port" => 5372,
			"user" => "guest",
			"password" => "guest",
			"vhost" => "/"
		);

		$this->args = $args;

		foreach ($args as $key => $value) {
			if (array_key_exists($key, $properties)) {
				$properties[$key] = $value;
			}
		}

		if (array_key_exists("conn", $this->args)) {
			$conn = $this->args["conn"];
		} else {
			$conn = new AMQPConnection(
				$properties["host"],
				$properties["port"],
				$properties["user"],
				$properties["password"],
				$properties["vhost"]
			);
		}

		$this->_conn = $conn;
		$this->channel = $conn->channel();
	}

	public function consume() {
		$this->channel->wait();
	}
}
