<?php

namespace TaskMQ;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Creates a worker (consumer) for receiving
 * tasks from a RabbitMQ queue
 */
class Worker extends BaseConnection {
	private $bindings = array();

	/**
	 * @param $args array Connection properties
	 */
	public function __construct($args) {
		parent::__construct($args);

		if (array_key_exists("queue", $this->args)) {
			$this->queue = $this->args["queue"];
		} else {
			$this->queue = "tasks";
		}

		if (array_key_exists("exchange", $this->args)) {
			$this->exchange = $this->args["exchange"];
		} else {
			$this->exchange = "amq.direct";
		}

		$this->channel->exchange_declare($this->exchange, "direct", false, true, false);
		$this->channel->queue_declare($this->queue, false, true, false, false);
		$this->channel->basic_qos(0, 1, FALSE);
	}

	/**
	 * @param $message AMQPMessage
	 */
	public function handleMessage(AMQPMessage $message) {
		$deliveryInfo = $message->delivery_info;
		$taskName = $deliveryInfo["routing_key"];

		$task = new Task(NULL, NULL);
		$task->fromMessage($taskName, $message);

		if (array_key_exists($taskName, $this->bindings)) {
			foreach ($this->bindings[$taskName] as $func) {
				$func($task);
			}
		}

		$this->channel->basic_ack($deliveryInfo["delivery_tag"]);
	}

	/**
	 * @param $taskName str
	 * @param $func function
	 */
	public function bind($taskName, $func) {
		$this->channel->queue_bind($this->queue, $this->exchange, $taskName);

		if (!array_key_exists($taskName, $this->bindings)) {
			$this->bindings[$taskName] = array();
		}

		array_push($this->bindings[$taskName], $func);
	}

	public function work() {
		$this->channel->basic_consume($this->queue, "", false, false, false, false, array($this, "handleMessage"));

		while (count($this->channel->callbacks)) {
			$this->consume();
		}
	}
}
