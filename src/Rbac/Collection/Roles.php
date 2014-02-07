<?php
/**
 * Roles Collection
 *
 * @package    Rbac\Collection
 * @author     Brandon Lamb <brandon@brandonlamb.com>
 * @author     George Boot <gboot@pxl.nl>
 * @author     Robert Pitt <rpitt@centiq.co.uk>
 */
namespace Rbac\Collection;

use Rbac\Manager;
use Rbac\AbstractCollection;

/**
 * Class Roles
 *
 * This class loads all roles based on give identity
 *
 * @package Rbac\Collection
 */
class Roles extends AbstractCollection implements Rbac\Interfaces\Collection
{
    /**
     * @type string
     */
    const ITEM_CLASS = '\\Rbac\\Role';

    /**
     * @type string
     */
	protected $cacheKey = 'Rbac.Collection.Roles.identity.';

    /**
     * @type int
     */
	protected $cacheTtl = 120;

	/**
	 * Fetch all allowed operatations for user
     *
	 * @return array
	 */
	protected function getData()
	{
		if($this->manager->cache())
		{
			/**
			 * Fetch the cache results
			 * @var Array
			 */
			$rows = $this->manager->cache()->retrieve($this->cacheKey . $this->identity);

			/**
			 * Parse the cache if we have one.
			 */
			if (isset($rows) && is_array($rows) && count($rows) > 0)
			{
				return $this->parse(static::ITEM_CLASS, $rows);
			}
		}

		/**
		 * Nothing found in cache, or cached array is empty, lookup from db
		 * @var string
		 */
		$sql = "
			SELECT
				DISTINCT
					rbac_role.name AS item_name,
					rbac_role.id AS item_id,
					rbac_role.description AS item_desc
				FROM rbac_role
				JOIN rbac_role_user ON (rbac_role_user.role_id = rbac_role.id)
				WHERE rbac_role_user.user_id = :id
				ORDER BY item_name ASC";

		/**
		 * Prepare the query for execution
		 */
		$statement = $this->manager->connection()->prepare($sql);
		$statement->execute(array($this->identity));
		$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		// Save to cache
		$this->manager->cache() && $this->manager->cache()->store($this->cacheKey . $this->identity, $rows, $this->cacheTtl);
		return $this->parse(static::ITEM_CLASS, $rows);
	}

	/**
	 * Add user id to array of roles
     *
	 * @param int $id
	 * @param array $roles
     *
	 * @return bool
	 */
	public function addUser($id, array $roles)
	{
		$id = (int) $id;
		foreach ($roles as $role) {
			$sql = "INSERT INTO acl_user_role (user_id, role_id) VALUES(?, (SELECT id FROM acl_role WHERE name = ?))";
			$stmt = $this->manager->connection()->prepare($sql);
			$stmt->execute(array($id, $role));
		}
	}
}