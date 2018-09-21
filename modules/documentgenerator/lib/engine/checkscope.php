<?php

namespace Bitrix\DocumentGenerator\Engine;

use Bitrix\DocumentGenerator\Document;
use Bitrix\DocumentGenerator\Template;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class CheckScope extends Base
{
	/**
	 * @param Event $event
	 * @return EventResult|null
	 * @throws \Bitrix\Main\SystemException
	 */
	public function onBeforeAction(Event $event)
	{
		$scope = null;
		$restServer = null;
		foreach($this->action->getArguments() as $name => $argument)
		{
			if($argument instanceof \CRestServer)
			{
				$restServer = $argument;
			}
			elseif($name == 'module')
			{
				$scope = $argument;
			}
			elseif($argument instanceof Document)
			{
				$template = $argument->getTemplate();
				if($template)
				{
					$scope = $template->MODULE_ID;
				}
			}
			elseif($argument instanceof Template)
			{
				$scope = $argument->MODULE_ID;
			}
		}

		if($scope && $restServer)
		{
			$scopes = $restServer->getAuthScope();
			if(!in_array($scope, $scopes))
			{
				$this->errorCollection[] = new Error('No access to scope '.$scope);
				return new EventResult(EventResult::ERROR, null, null, $this);
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function listAllowedScopes()
	{
		return [
			Controller::SCOPE_REST,
		];
	}
}