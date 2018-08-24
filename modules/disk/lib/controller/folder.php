<?php

namespace Bitrix\Disk\Controller;

use Bitrix\Disk;
use Bitrix\Disk\ZipNginx;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentTypeException;

class Folder extends BaseObject
{
	public function getAction(Disk\Folder $folder)
	{
		return $this->get($folder);
	}

	protected function get(Disk\BaseObject $folder)
	{
		if (!($folder instanceof Disk\Folder))
		{
			throw new ArgumentTypeException('folder', Disk\Folder::class);
		}

		$data = parent::get($folder);
		$data['folder'] = $data['object'];
		unset($data['object']);

		return $data;
	}

	public function renameAction(Disk\Folder $folder, $newName, $autoCorrect = false)
	{
		return $this->rename($folder, $newName, $autoCorrect);
	}

	public function markDeletedAction(Disk\Folder $folder)
	{
		return $this->markDeleted($folder);
	}

	public function deleteTreeAction(Disk\Folder $folder)
	{
		return $this->deleteFolder($folder);
	}

	public function restoreAction(Disk\BaseObject $folder)
	{
		return $this->restore($folder);
	}

	public function generateExternalLinkAction(Disk\Folder $folder)
	{
		return $this->generateExternalLink($folder);
	}

	public function disableExternalLinkAction(Disk\Folder $folder)
	{
		return $this->disableExternalLink($folder);
	}
	
	public function getAllowedOperationsRightsAction(Disk\Folder $folder)
	{
		return $this->getAllowedOperationsRights($folder);
	}

	public function downloadArchiveAction(Disk\Folder $folder)
	{
		if (!ZipNginx\Configuration::isEnabled())
		{
			$this->addError(new Disk\Internals\Error\Error('Work with mod_zip is disabled in module settings.'));

			return;
		}

		$storage = $folder->getStorage();
		if (!$storage)
		{
			$this->addError(new Disk\Internals\Error\Error("Could not find storage for folder."));

			return;
		}

		$securityContext = $storage->getSecurityContext($this->getCurrentUser()->getId());
		$zipArchive = ZipNginx\Archive::createFromFolder($folder, $securityContext);

		global $APPLICATION;
		$APPLICATION->restartBuffer();
		$zipArchive->send();
		Application::getInstance()->terminate();
	}
}