<?php

namespace TaskMQ;

/**
 * No-op data encoder
 */
class DummyEncoder {
	/**
	 * @param $input str
	 */
	public function encode($input) {
		return $input;
	}

	/**
	 * @param $input str
	 */
	public function decode($input) {
		return $input;
	}
}
