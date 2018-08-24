<?php

namespace Bitrix\Crm\Integration\DocumentGenerator\DataProvider;

use Bitrix\Crm\EntityAddress;
use Bitrix\Crm\EntityPreset;
use Bitrix\Crm\Format\RequisiteAddressFormatter;
use Bitrix\DocumentGenerator\DataProvider\HashDataProvider;

class Address extends HashDataProvider
{
	protected $typeId;

	/**
	 * Returns list of value names for this Provider.
	 *
	 * @return array
	 */
	public function getFields()
	{
		$fields = [];
		foreach($this->getAddressFields() as $placeholder)
		{
			$fields[$placeholder] = ['TITLE' => EntityAddress::getLabel($placeholder, $this->getTypeId())];
		}

		$fields['TEXT'] = [
			'TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_ADDRESS_TEXT_TITLE'),
			'VALUE' => function()
			{
				return $this->formatText();
			}
		];

		return $fields;
	}

	/**
	 * @return string
	 */
	protected function formatText()
	{
		return RequisiteAddressFormatter::formatByCountry($this->data, EntityPreset::getCurrentCountryId());
	}

	/**
	 * @return int|null
	 */
	protected function getTypeId()
	{
		if(isset($this->options['TYPE_ID']))
		{
			return $this->options['TYPE_ID'];
		}

		return null;
	}

	/**
	 * @return array
	 */
	protected function getAddressFields()
	{
		return [
			'ADDRESS_1',
			'ADDRESS_2',
			'CITY',
			'POSTAL_CODE',
			'REGION',
			'PROVINCE',
			'COUNTRY',
			//'COUNTRY_CODE',
		];
	}
}