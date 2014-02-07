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
     * @type CacheCache\Cache
     */
    protected $cache;

    /**
     * @type bool
     */
    protected $debug;

    /**
     * @type ops
     */
    protected $ops;

    /**
     * The constructor of this class
     */
    public function __construct()
	{
		$this->debug = false;
	}

	/**
	 * Get/set pdo connection
     *
	 * @param \Pdo $conn
	 * @return \Pdo
	 */
	public function connection(\Pdo $conn = null)
	{
		null !== $conn && $this->conn = $conn;
		return $this->conn;
	}

	/**
	 * Set/get debug flag
     *
	 * @param bool $debug
	 * @return bool
	 */
	public function debug($debug = null)
	{
		null !== $debug && $this->debug = (bool) $debug;
		return (bool) $this->debug;
	}

	/**
     * Set the cache object
     *
     * @param \CacheCache\Cache $cache
     * @return \CacheCache\Cache
     */
    public function setCache(\CacheCache\Cache $cache)
	{
		$this->cache = $cache;
		return $this->cache;
	}

	/**
	 * Return cache object
	 * @return CacheCache\Cache
	 */
	public function getCache()
	{
		return $this->cache;
	}

	/**
	 * Clear cache object
	 * @return $this
	 */
	public function clearCache()
	{
		$this->cache = null;
		return $this;
	}

	/**
	 * Check if operation access is allowed
     *
	 * @param string $access
	 * @param \Rbac\CollectionInterface $collection
	 * @return bool
	 */
	public function isAllowed($access, CollectionInterface $collection)
	{
		return $collection->isAllowed($access);
	}
}