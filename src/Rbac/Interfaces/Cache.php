<?php
/**
 * Collection Interface
 *
 * @package    Rbac
 * @author     Brandon Lamb <brandon@brandonlamb.com>
 * @author     George Boot <gboot@pxl.nl>
 * @author     Robert Pitt <rpitt@centiq.co.uk>
 */
namespace Rbac\Interfaces;

/**
 * Collection Interface
 * @package Rbac\Interfaces
 */
interface Cache
{
	/**
	 * @param array $key
	 * @return array|false
	 */
	function retrieve($key);

	/**
	 * @param array $key
	 * @param array $data
	 * @param int $expiration
	 * @return bool
	 */
	function store($key, $data, $ttl);

	/**
	 * @param null|array $key
	 * @return bool
	 */
	function clear($key = null);
}