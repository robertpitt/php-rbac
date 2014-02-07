<?php
/**
 * Task Collection
 *
 * @package    Rbac\Collection
 * @author     Brandon Lamb <brandon@brandonlamb.com>
 * @author     George Boot <gboot@pxl.nl>
 * @author     Robert Pitt <rpitt@centiq.co.uk>
 */
namespace Rbac\Collection;

/**
 * Import namespaces
 */
use Rbac\Manager;
use Rbac\AbstractCollection;

/**
 * Class Tasks
 *
 * This class loads all allowed tasks based on give, identity,
 * joining on the tables for roles and tasks.
 *
 * @package Rbac\Collection
 */
class Tasks extends AbstractCollection implements Rbac\Interfaces\Collection
{
  /**
   * @type string
   */
  const ITEM_CLASS = '\\Rbac\\Task';

  /**
   * @type string
   */
	protected $cacheKey = 'Rbac.Collection.Tasks.identity.';

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
      SELECT DISTINCT
        rbac_task.name AS item_name,
        rbac_task.id AS item_id,
        rbac_task.description AS item_desc
      FROM rbac_task
      JOIN rbac_operation_task ON (rbac_task.id = rbac_operation_task.task_id)
      JOIN rbac_role_task ON (rbac_operation_task.task_id = rbac_role_task.task_id)
      JOIN rbac_role ON (rbac_role.id = rbac_role_task.role_id)
      JOIN rbac_role_user ON (rbac_role_user.role_id = rbac_role_task.role_id)
      WHERE rbac_role_user.user_id = :id
      ORDER BY item_name ASC";

    $stmt = $this->manager->connection()->prepare($sql);
    $stmt->execute(array($this->identity));
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		// Save to cache
		$this->manager->getCache() && $this->manager->getCache()->set($this->cacheKey . $this->identity, $rows, $this->cacheTtl);

		return $this->parse(static::ITEM_CLASS, $rows);
	}
}