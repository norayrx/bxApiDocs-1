<?php
namespace Bitrix\Socialnetwork\Livefeed;

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;
use Bitrix\Socialnetwork\LogTable;

final class PhotogalleryAlbum extends Provider
{
	const PROVIDER_ID = 'PHOTO_ALBUM';
	const CONTENT_TYPE_ID = 'PHOTO_ALBUM';

	public static function getId()
	{
		return static::PROVIDER_ID;
	}

	public function getEventId()
	{
		return array('photo');
	}

	public function getType()
	{
		return Provider::TYPE_POST;
	}

	public function getCommentProvider()
	{
		$provider = new \Bitrix\Socialnetwork\Livefeed\LogComment();
		return $provider;
	}

	public function initSourceFields()
	{
		$elementId = $this->entityId;

		if (
			$elementId > 0
			&& Loader::includeModule('iblock')
		)
		{
			$res = SectionTable::getList(array(
				'filter' => array(
					'=ID' => $elementId
				),
				'select' => array('ID', 'NAME')
			));
			if ($element = $res->fetch())
			{
				$logId = false;

				$res = LogTable::getList(array(
					'filter' => array(
						'SOURCE_ID' => $elementId,
						'@EVENT_ID' => $this->getEventId(),
					),
					'select' => array('ID', 'URL')
				));
				if ($logEntryFields = $res->fetch())
				{
					$logId = intval($logEntryFields['ID']);
				}

				if ($logId)
				{
					$res = \CSocNetLog::getList(
						array(),
						array(
							'=ID' => $logId
						),
						false,
						false,
						array('ID', 'EVENT_ID', 'URL'),
						array(
							"CHECK_RIGHTS" => "Y",
							"USE_FOLLOW" => "N",
							"USE_SUBSCRIBE" => "N"
						)
					);
					if ($logFields = $res->fetch())
					{
						$this->setLogId($logFields['ID']);
						$this->setSourceFields(array_merge($element, array(
							'LOG_EVENT_ID' => $logFields['EVENT_ID'],
							'URL' => $logFields['URL']
						)));
						$title = $element['NAME'];
						$this->setSourceDescription($title);
						$this->setSourceTitle($title);
					}
				}
			}
		}
	}

	public static function canRead($params)
	{
		return true;
	}

	protected function getPermissions(array $post)
	{
		$result = self::PERMISSION_READ;

		return $result;
	}

	public function getLiveFeedUrl()
	{
		$pathToPhoto = '';

		if (
			($message = $this->getSourceFields())
			&& !empty($message)
		)
		{
			$pathToPhoto = $message['URL'];
		}

		return $pathToPhoto;
	}

}