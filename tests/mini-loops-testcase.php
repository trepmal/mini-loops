<?php

/**
 * Base unit test class for Mini Loops
 */
class MiniLoops_TestCase extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		global $mini_loops;
		$this->_ml = $mini_loops;
	}
}
