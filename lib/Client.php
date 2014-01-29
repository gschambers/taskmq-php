<?php

namespace TaskMQ;

/**
 * Creates a client for publishing
 * tasks to a RabbitMQ exchange
 */
class Client extends BaseConnection {
	public function submitTask(Task $task) {
		if (array_key_exists("exchange", $this->args)) {
			$exchange = $this->args["exchange"];
		} else {
			$exchange = "amq.direct";
		}

		$routingKey = $task->name;
		$message = $task->toMessage();

		$this->channel->basic_publish($message, $exchange, $routingKey);
	}
}
