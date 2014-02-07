<?php
/**
 * Class Manager
 *
 * @package    Rbac
 * @author     Brandon Lamb <brandon@brandonlamb.com>
 * @author     George Boot <gboot@pxl.nl>
 * @author     Robert Pitt <rpitt@centiq.co.uk>
 */
namespace Rbac;

/**
 * Class Manager
 * @package Rbac
 */
class Manager
{
	/**
	 * @type bool
	 */
	protected $debug = false;

	/**
	 * Cache Interface
	 * @type Interfaces\Cache
	 */
	protected $cache;

	/**
	 * @type ops
	 */
	protected $ops;

	/**
	 * PDO Connection
	 * @var \Pdo
	 */
	protected $pdo;

	/**
	 * The constructor of this class
	 * @param Pdo $connection PDO Connection
	 */
	public function __construct(\Pdo $connection)
	{
		$this->connection($connection);
	}

	/**
	 * Get/set pdo connection
	 * @param \Pdo $connection
	 * @return \Pdo
	 */
	public function connection(\Pdo $connection = null)
	{
		if($connection !== null)
		{
			$this->connection = $connection;
		}

		return $this->connection;
	}

	/**
	 * Set/get debug flag
	 *
	 * @param bool $debug
	 * @return bool
	 */
	public function debug($debug = null)
	{
		if($debug !== null)
		{
			$this->debug = (bool)$debug;
		}

		return $this->debug;
	}

	/**
	 * Set the cache object
	 * @param Interfaces\Cache $cache Cache Interface
	 */
	public function cache(Interfaces\Cache $cache = null)
	{
		if($cache !== null)
		{
			$this->cache = $cache;
		}

		return $this->cache;
	}

	/**
	 * Check if operation access is allowed
	 * @param string $access
	 * @param \Rbac\Interfaces\Collection $collection
	 * @return bool
	 */
	public function isAllowed($access, Interfaces\Collection $collection)
	{
		return $collection->isAllowed($access);
	}
}