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
use Rbac\CollectionInterface;
use RedBean_Facade as R;

/**
 * Class Operations
 *
 * This class loads all allowed operations based on give identity,
 * joining on the tables for roles and tasks.
 *
 * @package Rbac\Collection
 */
class Operations extends AbstractCollection implements CollectionInterface
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
		$this->manager->getCache() && $rows = $this->manager->getCache()->get($this->cacheKey . $this->identity);
		if (isset($rows) && is_array($rows) && count($rows) > 0) {
			return $this->parse(static::ITEM_CLASS, $rows);
		}

		// Nothing found in cache, or cached array is empty, lookup from db
        $rows = R::getAll("
          SELECT
            DISTINCT `operation`.`name` AS item_name,
            `operation`.`id` AS item_id,
            `operation`.`description` AS item_desc
          FROM `operation`
          JOIN `operation_task` ON (`operation`.`id` = `operation_task`.`operation_id`)
          JOIN `role_task` ON (`operation_task`.`task_id` = `role_task`.`task_id`)
          JOIN `role` ON (`role`.`id` = `role_task`.`role_id`)
          JOIN `role_user` ON (`role_user`.`role_id` = `role_task`.`role_id`)
          WHERE `role_user`.`id` = :id
          ORDER BY item_name ASC
        ", [
            ':id' => $this->identity
        ]);

		// Save to cache
		$this->manager->getCache() && $this->manager->getCache()->set($this->cacheKey . $this->identity, $rows, $this->cacheTtl);

		return $this->parse(static::ITEM_CLASS, $rows);
	}
}