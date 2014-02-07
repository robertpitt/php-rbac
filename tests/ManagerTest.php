<?php

/**
 * Helper Mock
 */
class PDOMock extends \PDO {public function __construct() {}};

/**
 * Class Manager
 * @package Rbac
 */
class ManagerTest extends PHPUnit_Framework_TestCase
{
	protected $manager;

	public function setUp()
	{
		$this->manager = new Rbac\Manager(
			$this->getMockBuilder('PDOMock')->getMock()
        );
	}

	/**
	 * Test debug
	 */
	public function testDebug()
	{
		/**
		 * Should default to false
		 */
		$this->assertFalse($this->manager->debug());

		/**
		 * Set the debugger
		 */
		$this->manager->debug(true);

		/**
		 * Should now be true
		 */
		$this->assertTrue($this->manager->debug(true));
		$this->assertTrue($this->manager->debug());
	}


	/**
	 * Test Connection
	 */
	public function testConnection()
	{
		$this->assertInstanceOf("PDO", $this->manager->connection());
	}

	/**
	 * @todo  Test Cache
	 */
	public function testCache()
	{

	}
}