<?php

namespace Rbac\Collection;

use Rbac\Manager;
use Rbac\AbstractCollection;
use Rbac\CollectionInterface;
use RedBean_Facade as R;

/**
 * Class Tasks
 *
 * This class loads all allowed tasks based on give, identity,
 * joining on the tables for roles and tasks.
 *
 * @package Rbac\Collection
 */
class Tasks extends AbstractCollection implements CollectionInterface
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
		$this->manager->getCache() && $rows = $this->manager->getCache()->get($this->cacheKey . $this->identity);
		if (isset($rows) && is_array($rows) && count($rows) > 0) {
			return $this->parse(static::ITEM_CLASS, $rows);
		}

		// Nothing found in cache, or cached array is empty, lookup from db
        $rows = R::getAll("
          SELECT
            DISTINCT `task`.`name` AS item_name,
            `task`.`id` AS item_id,
            `task`.`description` AS item_desc
          FROM `task`
          JOIN `operation_task` ON (`task`.`id` = `operation_task`.`task_id`)
          JOIN `role_task` ON (`operation_task`.`task_id` = `role_task`.`task_id`)
          JOIN `role` ON (`role`.`id` = `role_task`.`role_id`)
          JOIN `role_user` ON (`role_user`.`role_id` = `role_task`.`role_id`)
          WHERE `role_user`.`user_id` = :id
          ORDER BY item_name ASC
        ", [
            ':id' => $this->identity
        ]);

		// Save to cache
		$this->manager->getCache() && $this->manager->getCache()->set($this->cacheKey . $this->identity, $rows, $this->cacheTtl);

		return $this->parse(static::ITEM_CLASS, $rows);
	}
}