<?php
/**
 * Created by PhpStorm.
 * User: maxyc
 * Date: 17.05.18
 * Time: 10:28
 */

namespace Bitrix\Recyclebin\Internals;

use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Recyclebin\Internals\Models\RecyclebinDataTable;
use Bitrix\Recyclebin\Internals\Models\RecyclebinTable;

class Entity
{
	private $id;
	private $siteId;
	private $entityId;
	private $entityType;
	private $moduleId;
	private $ownerId;
	private $title;
	private $data = [];

	public function __construct($entityId, $entityType, $moduleId)
	{
		$this->setEntityType($entityType);
		$this->setEntityId($entityId);
		$this->setModuleId($moduleId);
	}

	/**
	 * @return Result
	 */
	public function save()
	{
		$result = new Result;

		$data = [
			'NAME'        => $this->getTitle(),
			'SITE_ID'     => $this->getSiteId(),
			'ENTITY_ID'   => $this->getEntityId(),
			'ENTITY_TYPE' => $this->getEntityType(),
			'MODULE_ID'   => $this->getModuleId(),
			'USER_ID'     => $this->getOwnerId()
		];

		try
		{
			$recyclebin = RecyclebinTable::add($data);

			$result->setData(['ID'=>$recyclebin->getId()]);

			if (!$recyclebin->isSuccess())
			{
				$result->addErrors($recyclebin->getErrors());
			}
			else
			{
				$this->setId($recyclebin->getId());

				foreach ($this->getData() as $action => $data)
				{
					RecyclebinDataTable::add(
						[
							'RECYCLEBIN_ID' => $this->getId(),
							'ACTION'   => $action,
							'DATA'     => serialize($data)
						]
					);
				}
			}
		}
		catch (\Exception $e)
		{
			$result->addError(new Error($e->getMessage(), $e->getCode()));
		}

		return $result;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function getSiteId()
	{
		if (!$this->siteId)
		{
			$this->setSiteId(SITE_ID);
		}

		return $this->siteId;
	}

	/**
	 * @param mixed $siteId
	 */
	public function setSiteId($siteId)
	{
		$this->siteId = $siteId;
	}

	/**
	 * @return string|int
	 */
	public function getEntityId()
	{
		return $this->entityId;
	}

	/**
	 * @param string|int $entityId
	 */
	public function setEntityId($entityId)
	{
		$this->entityId = $entityId;
	}

	/**
	 * @return string
	 */
	public function getEntityType()
	{
		return $this->entityType;
	}

	/**
	 * @param string $entityType
	 */
	public function setEntityType($entityType)
	{
		$this->entityType = $entityType;
	}

	/**
	 * @return string
	 */
	public function getModuleId()
	{
		return $this->moduleId;
	}

	/**
	 * @param string $moduleId
	 */
	public function setModuleId($moduleId)
	{
		$this->moduleId = $moduleId;
	}

	/**
	 * @return int
	 */
	public function getOwnerId()
	{
		if (!$this->ownerId)
		{
			$this->setOwnerId(User::getId());
		}

		return $this->ownerId;
	}

	/**
	 * @param int $ownerId
	 */
	public function setOwnerId($ownerId)
	{
		$this->ownerId = $ownerId;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	public function setData(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param string $action Name of current action/name/index of data
	 * @param array $data
	 */
	public function add($action, array $data)
	{
		$this->data[$action] = $data;
	}

}