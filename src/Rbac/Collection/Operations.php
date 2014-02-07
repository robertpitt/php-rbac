<?php
/**
 * Operations Collection
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
 * Class Operations
 *
 * This class loads all allowed operations based on give identity,
 * joining on the tables for roles and tasks.
 *
 * @package Rbac\Collection
 */
class Operations extends AbstractCollection implements Rbac\Interfaces\Collection
{
  /**
   * @type string
   */
  const ITEM_CLASS = '\\Rbac\\Op';

    /**
     * @type string
     */
	protected $cacheKey = 'Rbac.Collection.Operations.identity.';

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
		// Get results from cache if they exist
    if($this->manager->cache())
    {
      $rows = $this->manager->cache()->retrieve($this->cacheKey . $this->identity);
      if (isset($rows) && is_array($rows) && count($rows) > 0)
      {
        return $this->parse(static::ITEM_CLASS, $rows);
      }
    }

    /**
     * Build the query
     */
    $sql = "
      SELECT
        DISTINCT
          operation.name        AS item_name,
          operation.id          AS item_id,
          operation.description AS item_desc
        FROM operation
        JOIN operation_task ON (operation.id = operation_task.operation_id)
        JOIN role_task ON (operation_task.task_id = role_task.task_id)
        JOIN role ON (role.id = role_task.role_id)
        JOIN role_user ON (role_user.role_id = role_task.role_id)
        WHERE role_user.id = :id
        ORDER BY item_name ASC
    ";

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
}