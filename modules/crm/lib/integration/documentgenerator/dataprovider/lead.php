<?php

namespace Bitrix\Crm\Integration\DocumentGenerator\DataProvider;

use Bitrix\Crm\LeadTable;
use Bitrix\DocumentGenerator\Nameable;

class Lead extends ProductsDataProvider implements Nameable
{
	protected $products;

	/**
	 * @return array
	 */
	public function getFields()
	{
		$fields = parent::getFields();
		$fields['STATUS'] = ['TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_LEAD_STATUS_TITLE'),];
		$fields['SOURCE'] = ['TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_LEAD_SOURCE_TITLE'),];
		$fields['IMOL'] = ['TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_LEAD_IMOL_TITLE'),];

		return $fields;
	}

	/**
	 * @return string
	 */
	public static function getLangName()
	{
		return GetMessage('CRM_DOCGEN_DATAPROVIDER_LEAD_TITLE');
	}

	/**
	 * @param int $userId
	 * @return bool
	 */
	public function hasAccess($userId)
	{
		if($this->isLoaded())
		{
			$userPermissions = new \CCrmPerms($userId);
			return \CCrmLead::CheckReadPermission($this->source, $userPermissions);
		}

		return false;
	}

	/**
	 * @return string
	 */
	protected function getTableClass()
	{
		return LeadTable::class;
	}

	/**
	 * @return array
	 */
	protected function getHiddenFields()
	{
		return array_merge(parent::getHiddenFields(), [
			'STATUS_ID',
			'STATUS_BY',
			'IS_CONVERT',
			'SHORT_NAME',
			'LOGIN',
			'SOURCE_ID',
			'SOURCE_BY',
			'ASSIGNED_BY_ID',
			'ASSIGNED_BY',
			'CREATED_BY_ID',
			'CREATED_BY',
			'MODIFY_BY_ID',
			'MODIFY_BY',
			'EVENT_RELATION',
			'HAS_EMAIL',
			'HAS_PHONE',
			'HAS_IMOL',
			'SEARCH_CONTENT',
			'DATE_CREATE_SHORT',
			'DATE_MODIFY_SHORT',
			'FACE_ID',
		]);
	}

	protected function getGetListParameters()
	{
		return array_merge_recursive(parent::getGetListParameters(), [
			'select' => [
				'STATUS' => 'STATUS_BY.NAME',
				'SOURCE' => 'SOURCE_BY.NAME',
			],
		]);
	}

	public function getCrmOwnerType()
	{
		return \CCrmOwnerType::Lead;
	}

	protected function getCrmProductOwnerType()
	{
		return 'L';
	}

	/**
	 * @return string
	 */
	protected function getUserFieldEntityID()
	{
		return \CCrmLead::GetUserFieldEntityID();
	}
}