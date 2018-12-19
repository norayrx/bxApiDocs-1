<?php

namespace Bitrix\DocumentGenerator\Model;

use Bitrix\DocumentGenerator\Body;
use Bitrix\DocumentGenerator\DataProvider\Filterable;
use Bitrix\DocumentGenerator\DataProviderManager;
use Bitrix\DocumentGenerator\Template;
use Bitrix\Main;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TemplateTable extends FileModel
{
	protected static $fileFieldNames = [
		'FILE_ID',
	];

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_documentgenerator_template';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true,
			]),
			new Main\Entity\BooleanField('ACTIVE', [
				'values' => array('Y', 'N'),
				'default_value' => 'Y',
			]),
			new Main\Entity\StringField('NAME', [
				'required' => true,
			]),
			new Main\Entity\StringField('CODE'),
			new Main\Entity\StringField('REGION'),
			new Main\Entity\IntegerField('SORT', [
				'default_value' => 500,
			]),
			new Main\Entity\DatetimeField('CREATE_TIME', [
				'required' => true,
				'default_value' => function(){return new Main\Type\DateTime();},
			]),
			new Main\Entity\DatetimeField('UPDATE_TIME', [
				'default_value' => function(){return new Main\Type\DateTime();},
			]),
			new Main\Entity\IntegerField('CREATED_BY'),
			new Main\Entity\IntegerField('UPDATED_BY'),
			new Main\Entity\StringField('MODULE_ID'),
			new Main\Entity\IntegerField('FILE_ID', [
				'required' => true,
			]),
			new Main\Entity\StringField('BODY_TYPE', [
				'required' => true,
				'validation' => function()
				{
					return [
						function($value)
						{
							if(is_a($value, Body::class, true))
							{
								return true;
							}
							else
							{
								return Loc::getMessage('DOCUMENTGENERATOR_MODEL_TEMPLATE_CLASS_VALIDATION', ['#CLASSNAME#' => $value, '#PARENT#' => Body::class]);
							}
						},
					];
				},
			]),
			new Main\Entity\ReferenceField(
				'PROVIDER',
				'\Bitrix\DocumentGenerator\Model\TemplateProvider',
				['=this.ID' => 'ref.TEMPLATE_ID']
			),
			new Main\Entity\ReferenceField(
				'USER',
				'\Bitrix\DocumentGenerator\Model\TemplateUser',
				['=this.ID' => 'ref.TEMPLATE_ID']
			),
			new Main\Entity\IntegerField('NUMERATOR_ID'),
			new Main\Entity\BooleanField('WITH_STAMPS', [
				'values' => array('Y', 'N'),
				'default_value' => 'N',
			]),
			new Main\Entity\BooleanField('IS_DELETED', [
				'values' => array('Y', 'N'),
				'default_value' => 'N',
			]),
		];
	}

	/**
	 * @param string $className
	 * @param null $userId
	 * @param mixed $value
	 * @param bool $activeOnly
	 * @return array
	 * @throws Main\ArgumentException
	 * @throws Main\ObjectPropertyException
	 * @throws Main\SystemException
	 */
	public static function getListByClassName($className, $userId = null, $value = ' ', $activeOnly = true)
	{
		$filterProvider = $className;
		if(is_a($className, Filterable::class, true))
		{
			/** @var Filterable $provider */
			$provider = DataProviderManager::getInstance()->getDataProvider($className, $value, ['isLightMode' => true]);
			if($provider)
			{
				$filterProvider = $provider->getFilterString();
			}
		}
		$filterProvider = str_replace("\\", "\\\\", strtolower($filterProvider));
		$filter = Main\Entity\Query::filter()->whereLike('PROVIDER.PROVIDER', $filterProvider)->where('IS_DELETED', 'N');
		if($activeOnly)
		{
			$filter->where('ACTIVE', 'Y');
		}
		if($userId > 0)
		{
			$filter->where(
				Main\Entity\Query::filter()
					->logic('or')
					->whereIn('USER.ACCESS_CODE', Main\UserAccessTable::query()->addSelect('ACCESS_CODE')->where('USER_ID', $userId))
					->where('USER.ACCESS_CODE', TemplateUserTable::ALL_USERS)
			);
		}
		return static::getList([
			'order' => ['SORT' => 'asc', 'ID' => 'asc'],
			'filter' => $filter,
			'cache' => ['ttl' => 1800],
			'group' => ['ID'],
		])->fetchAll();
	}

	/**
	 * @param Event $event
	 * @return Main\EventResult
	 */
	public static function onBeforeDelete(Event $event)
	{
		$id = $event->getParameter('primary')['ID'];
		$data = static::getById($id)->fetch();

		foreach(static::$fileFieldNames as $name)
		{
			if($data[$name])
			{
				static::$filesToDelete[] = $data[$name];
			}
		}

		TemplateProviderTable::deleteByTemplateId($id);
		TemplateUserTable::delete($id);

		return parent::onBeforeDelete($event);
	}

	/**
	 * @param Event $event
	 * @return Main\Entity\EventResult
	 */
	public static function onAfterAdd(Event $event)
	{
		parent::onAfterAdd($event);
		static::normalizeBody($event->getParameter('primary')['ID']);

		return new Main\Entity\EventResult();
	}

	/**
	 * @param Event $event
	 * @return Main\Entity\EventResult
	 * @throws \Exception
	 */
	public static function onAfterUpdate(Event $event)
	{
		if(!empty(static::$filesToDelete))
		{
			static::normalizeBody($event->getParameter('primary')['ID']);
		}
		return parent::onAfterUpdate($event);
	}

	/**
	 * Normalizes Body of the template for correct work.
	 *
	 * @param int $templateId
	 */
	public static function normalizeBody($templateId)
	{
		if(!$templateId)
		{
			return;
		}
		$template = Template::loadById($templateId);
		if(!$template)
		{
			return;
		}
		$body = $template->getBody();
		if(!$body)
		{
			return;
		}

		$body->normalizeContent();
		FileTable::updateContent($template->FILE_ID, $body->getContent(), ['fileName' => $template->getFileName(), 'contentType' => $body->getFileMimeType()]);
	}

	/**
	 * @param mixed $primary
	 * @param bool $isForever
	 * @return Main\ORM\Data\DeleteResult
	 * @throws \Exception
	 */
	public static function delete($primary, $isForever = false)
	{
		if(!$isForever)
		{
			$documents = DocumentTable::getCount(['TEMPLATE_ID' => $primary]);
			if($documents == 0)
			{
				$isForever = true;
			}
		}
		if($isForever)
		{
			return parent::delete($primary);
		}

		$deleteResult = new Main\ORM\Data\DeleteResult();
		$result = static::update($primary, ['IS_DELETED' => 'Y', 'FILE_ID' => 0]);
		if(!$result->isSuccess())
		{
			$deleteResult->addErrors($result->getErrors());
		}

		return $deleteResult;
	}
}