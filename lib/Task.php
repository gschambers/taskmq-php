<?php

namespace TaskMQ;
use PhpAmqpLib\Message\AMQPMessage;

class Task {
	/**
	 * Submits a task request via a RabbitMQ exchange
	 *
	 * @param $name str
	 * @param $data str
	 * @param $encoder Class
	 */
	public function __construct($name, $data, $encoder=NULL) {
		if ($encoder !== NULL) {
			$this->encoder = $encoder;
		} else {
			$this->encoder = new DummyEncoder();
		}

		$this->name = $name;
		$this->data = $this->encoder->encode($data);
	}

	public function toMessage() {
		return new AMQPMessage($this->data);
	}

	/**
	 * Bootstraps task from AMQP message
	 *
	 * @param $routingKey str
	 * @param $message AMQPMessage
	 */
	public function fromMessage($routingKey, $message) {
		$name = $routingKey;
		$data = $this->encoder->decode($message->body);

		$this->name = $name;
		$this->data = $data;
	}
}
