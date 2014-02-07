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
interface Collection
{
	/**
     * Constructor
     *
     * @param Manager $manager
     * @param int     $id
     * @param array   $data
     */
    public function __construct(Manager $manager, $id = 0, array $data = array());

	/**
	 * Set user identity
     *
	 * @param int $identity
	 * @return CollectionInterface
	 */
	public function setIdentity($identity);

	/**
	 * Get the user identity
     *
	 * @return int
	 */
	public function getIdentity();

	/**
	 * Get collection data array
	 * 
	 * @return array
	 */
	public function data();

	/**
	 * Check if the access to the context is allowed
     *
	 * @param string $context
	 * @return bool
	 */
	public function isAllowed($context);
}