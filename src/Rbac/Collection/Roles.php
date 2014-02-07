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
use Rbac\CollectionInterface;
use RedBean_Facade as R;

/**
 * Class Roles
 *
 * This class loads all roles based on give identity
 *
 * @package Rbac\Collection
 */
class Roles extends AbstractCollection implements CollectionInterface
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
    if($this->manager->getCache())
    {
      $rows = $this->manager->getCache()->get($this->cacheKey . $this->identity);
      if (isset($rows) && is_array($rows) && count($rows) > 0)
      {
        return $this->parse(static::ITEM_CLASS, $rows);
      }
    }

		// Nothing found in cache, or cached array is empty, lookup from db
        $sql = "
        	SELECT
            	DISTINCT
            		role.name AS item_name,
            		role.id AS item_id,
            		role.description AS item_desc
			FROM role
			JOIN role_user ON (role_user.role_id = role.id)
			WHERE role_user.user_id = :id
			ORDER BY item_name ASC";

		/**
		 * Prepare the query for execution
		 */
		$statement = $this->manager->connection()->prepare($sql);
		$statement->execute(array($this->identity));
		$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		// Save to cache
		$this->manager->getCache() && $this->manager->getCache()->set($this->cacheKey . $this->identity, $rows, $this->cacheTtl);

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