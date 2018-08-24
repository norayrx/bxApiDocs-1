<?php

namespace Bitrix\ImOpenLines;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Network
{
	const MODULE_ID = 'imopenlines';
	const EXTERNAL_AUTH_ID = 'imconnector';

	private $error = null;

	public function __construct()
	{
		$this->error = new Error(null, '', '');
	}

	public function sendMessage($lineId, $fields)
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		\Bitrix\ImOpenLines\Log::write($fields, 'NETWORK ANSWER');

		$userArray = Array();
		if ($fields['message']['user_id'] > 0)
		{
			$user = \Bitrix\Im\User::getInstance($fields['message']['user_id']);

			$avatarUrl = '';
			if ($user->getAvatarId())
			{
				$arFileTmp = \CFile::ResizeImageGet(
					$user->getAvatarId(),
					array('width' => 300, 'height' => 300),
					BX_RESIZE_IMAGE_EXACT,
					false,
					false,
					true
				);
				$avatarUrl = substr($arFileTmp['src'], 0, 4) == 'http'? $arFileTmp['src']: \Bitrix\ImOpenLines\Common::getServerAddress().$arFileTmp['src'];
			}

			$userArray = Array(
				'ID' => $user->getId(),
				'NAME' => $user->getName(false),
				'LAST_NAME' => $user->getLastName(false),
				'PERSONAL_GENDER' => $user->getGender(),
				'PERSONAL_PHOTO' => $avatarUrl
			);

			if (function_exists('customImopenlinesOperatorNames') && !$user->isExtranet()) // Temporary hack :(
			{
				$userArray = customImopenlinesOperatorNames($lineId, $userArray);
			}
		}

		\Bitrix\ImBot\Service\Openlines::operatorMessageAdd(Array(
			"LINE_ID" => $lineId,
			"GUID" => $fields['chat']['id'],
			"MESSAGE_ID" => $fields['im']['message_id'],
			"MESSAGE_TEXT" => $fields['message']['text'],
			"FILES" => $fields['message']['files'],
			"ATTACH" => $fields['message']['attachments'],
			"PARAMS" => $fields['message']['params'],
			"USER" => $userArray
		));

		return true;
	}

	public function updateMessage($lineId, $fields)
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		\Bitrix\ImOpenLines\Log::write($fields, 'NETWORK UPDATE MESSAGE');

		\Bitrix\ImBot\Service\Openlines::operatorMessageUpdate(Array(
			"LINE_ID" => $lineId,
			"GUID" => $fields['chat']['id'],
			"MESSAGE_ID" => $fields['im']['message_id'],
			"MESSAGE_TEXT" => $fields['message']['text'],
			"CONNECTOR_MID" => $fields['message']['id'][0],
			"FILES" => $fields['message']['files'],
			"ATTACH" => $fields['message']['attachments'],
			"PARAMS" => $fields['message']['params'],
		));

		return true;
	}

	public function deleteMessage($lineId, $fields)
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		\Bitrix\ImOpenLines\Log::write($fields, 'NETWORK DELETE MESSAGE');

		\Bitrix\ImBot\Service\Openlines::operatorMessageDelete(Array(
			"LINE_ID" => $lineId,
			"GUID" => $fields['chat']['id'],
			"MESSAGE_ID" => $fields['im']['message_id'],
			"CONNECTOR_MID" => is_array($fields['message']['id'])? $fields['message']['id'][0]: $fields['message']['id']
		));

		return true;
	}

	public function sendStatusWriting($lineId, $fields)
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		\Bitrix\ImOpenLines\Log::write($fields, 'NETWORK START WRITING (SEND)');

		$userArray = Array();
		if ($fields['user'] > 0)
		{
			$user = \Bitrix\Im\User::getInstance($fields['message']['user_id']);

			$avatarUrl = '';
			if ($user->getAvatarId())
			{
				$arFileTmp = \CFile::ResizeImageGet(
					$user->getAvatarId(),
					array('width' => 300, 'height' => 300),
					BX_RESIZE_IMAGE_EXACT,
					false,
					false,
					true
				);
				$avatarUrl = substr($arFileTmp['src'], 0, 4) == 'http'? $arFileTmp['src']: \Bitrix\ImOpenLines\Common::getServerAddress().$arFileTmp['src'];
			}

			$userArray = Array(
				'ID' => $user->getId(),
				'NAME' => $user->getName(false),
				'LAST_NAME' => $user->getLastName(false),
				'PERSONAL_GENDER' => $user->getGender(),
				'PERSONAL_PHOTO' => $avatarUrl
			);

			if (function_exists('customImopenlinesOperatorNames') && !$user->isExtranet()) // Temporary hack :(
			{
				$userArray = customImopenlinesOperatorNames($lineId, $userArray);
			}
		}

		\Bitrix\ImBot\Service\Openlines::operatorStartWriting(Array(
			"LINE_ID" => $lineId,
			"GUID" => $fields['chat']['id'],
			"USER" => $userArray
		));

		return true;
	}




	public function onReceiveCommand($command, $params)
	{
		$result = null;

		if ($command == 'clientMessageAdd')
		{
			$result = $this->executeClientMessageAdd($params);
		}
		else if ($command == 'clientMessageUpdate')
		{
			$result = $this->executeClientMessageUpdate($params);
		}
		else if ($command == 'clientMessageDelete')
		{
			$result = $this->executeClientMessageDelete($params);
		}
		else if ($command == 'clientMessageReceived')
		{
			$result = $this->executeClientMessageReceived($params);
		}
		else if ($command == 'clientStartWriting')
		{
			$result = $this->executeClientStartWriting($params);
		}
		else if ($command == 'clientSessionVote')
		{
			$result = $this->executeClientSessionVote($params);
		}
		else if ($command == 'clientChangeLicence')
		{
			$result = $this->executeClientChangeLicence($params);
		}

		return $result;
	}

	private function executeClientSessionVote($params)
	{
		if (!isset($params['USER']))
			return false;

		$userId = $this->getUserId($params['USER'], false);
		if (!$userId)
			return false;

		\Bitrix\ImOpenLines\Log::write($params, 'NETWORK SESSION VOTE');

		if (!\Bitrix\Main\Loader::includeModule('im'))
			return false;

		$messageParams = \CIMMessageParam::Get($params['MESSAGE_ID']);
		if ($messageParams['IMOL_VOTE'] != $params['SESSION_ID'])
			return false;

		$params['ACTION'] = $params['ACTION'] == 'dislike'? 'dislike': 'like';

		$result = \Bitrix\ImOpenlines\Session::voteAsUser($messageParams['IMOL_VOTE'], $params['ACTION']);
		if ($result)
		{
			\CIMMessageParam::Set($params['MESSAGE_ID'], Array('IMOL_VOTE' => $params['ACTION']));
			\CIMMessageParam::SendPull($params['MESSAGE_ID'], Array('IMOL_VOTE'));
		}

		return true;
	}

	private function executeClientChangeLicence($params)
	{
		\Bitrix\ImOpenLines\Log::write($params, 'NETWORK CHANGE LICENCE');

		if (!\Bitrix\Main\Loader::includeModule('im'))
			return false;

		$params['GUID'] = array_map(function($value){return 'network|'.$value;}, $params['GUID']);

		$users =\Bitrix\Main\UserTable::getList(Array(
			'select' => Array('ID'),
			'filter' => Array(
				'=EXTERNAL_AUTH_ID' => self::EXTERNAL_AUTH_ID,
				'=XML_ID' => $params['GUID']
			)
		))->fetchAll();

		if (empty($users))
		{
			return false;
		}
		$users = array_map(function($value){return $value['ID'];}, $users);

		$orm = \Bitrix\Imopenlines\Model\SessionTable::getList(Array(
			'select' => Array('ID', 'CONFIG_ID', 'USER_ID', 'SOURCE', 'CHAT_ID', 'USER_CODE'),
			'filter' => Array(
				'=USER_ID' => $users,
				'=CLOSED' => 'N',
			)
		));
		while($row = $orm->fetch())
		{
			Im::addMessage(Array(
				"TO_CHAT_ID" => $row['CHAT_ID'],
				'MESSAGE' => Loc::getMessage('IMOL_NETWORK_TARIFF_DIALOG_CLOSE'),
				'SYSTEM' => 'Y',
				'SKIP_COMMAND' => 'Y',
				'RECENT_ADD' => 'N',
				"PARAMS" => Array(
					"CLASS" => "bx-messenger-content-item-system"
				),
			));

			$session = new Session();
			$result = $session->start(array_merge($row, Array(
				'SKIP_CREATE' => 'Y',
			)));
			if (!$result)
			{
				return false;
			}

			$session->update(Array(
				'WAIT_ACTION' => 'Y',
				'WAIT_ANSWER' => 'N',
			));
			$session->finish();
		}

		return true;
	}

	private function executeClientStartWriting($params)
	{
		if (!isset($params['USER']))
			return false;

		$userId = $this->getUserId($params['USER'], false);
		if (!$userId)
			return false;

		\Bitrix\ImOpenLines\Log::write($params, 'NETWORK START WRITING');

		$event = new \Bitrix\Main\Event('imconnector', 'OnReceivedStatusWrites', Array(
			'user' => $userId,
			'connector' => 'network',
			'line' => $params['LINE_ID'],
			'chat' => Array('id' => $params['GUID'])
		));
		$event->send();

		return true;
	}

	private function executeClientMessageAdd($params)
	{
		if (!isset($params['USER']))
			return false;

		if ($params['MESSAGE_TYPE'] != 'P')
			return false;

		\Bitrix\ImOpenLines\Log::write($params, 'NETWORK GET');

		$userId = $this->getUserId($params['USER']);

		$message = Array(
			'id' => $params['MESSAGE_ID'],
			'date' => "",
			'text' => $params['MESSAGE_TEXT'],
			'fileLinks' => $params['FILES'],
			'attach' => $params['ATTACH'],
			'params' => $params['PARAMS'],
		);

		$params['USER']['FULL_NAME'] = \CUser::FormatName(\CSite::GetNameFormat(false), $params['USER'], true, false);

		$extraFields = Array();
		$description = '[B]'.Loc::getMessage('IMOL_NETWORK_NAME').'[/B]: '.$params['USER']['FULL_NAME'].'[BR]';
		if (isset($params['USER']['WORK_POSITION']) && !empty($params['USER']['WORK_POSITION']))
		{
			$description .= '[B]'.Loc::getMessage('IMOL_NETWORK_POST').'[/B]: '.$params['USER']['WORK_POSITION'].'[BR]';
		}
		if (isset($params['USER']['EMAIL']) && !empty($params['USER']['EMAIL']))
		{
			$description .= '[B]'.Loc::getMessage('IMOL_NETWORK_EMAIL').'[/B]: '.$params['USER']['EMAIL'].'[BR]';
		}
		if (isset($params['USER']['TARIFF_LEVEL']) && !empty($params['USER']['TARIFF_LEVEL']))
		{
			$description .= '[BR][B]'.Loc::getMessage('IMOL_NETWORK_TARIFF_LEVEL').'[/B]: '.Loc::getMessage($params['USER']['TARIFF_LEVEL'] == 'paid'? 'IMOL_NETWORK_TARIFF_LEVEL_PAID': 'IMOL_NETWORK_TARIFF_LEVEL_FREE').'[BR]';
		}
		if (isset($params['USER']['TARIFF']) && !empty($params['USER']['TARIFF']))
		{
			if (!empty($params['USER']['TARIFF_NAME']))
				$description .= '[B]'.Loc::getMessage('IMOL_NETWORK_TARIFF').'[/B]: '.$params['USER']['TARIFF_NAME'].' ('.$params['USER']['TARIFF'].')[BR]';
			else
				$description .= '[B]'.Loc::getMessage('IMOL_NETWORK_TARIFF').'[/B]: '.$params['USER']['TARIFF'].'[BR]';

			$extraFields['EXTRA_TARIFF'] = $params['USER']['TARIFF'];
		}
		if (isset($params['USER']['REGISTER']) && !empty($params['USER']['REGISTER']))
		{
			$daysAgo = intval((time() - $params['USER']['REGISTER']) / 60 / 60 / 24);
			$daysAgo = ($daysAgo > 0? $daysAgo: 1);
			$description .= '[B]'.Loc::getMessage('IMOL_NETWORK_REGISTER').'[/B]: '.$daysAgo.'[BR]';
			$extraFields['EXTRA_REGISTER'] = $daysAgo;
		}
		if (isset($params['USER']['DEMO']) && !empty($params['USER']['DEMO']))
		{
			$daysAgo = intval((time() - $params['USER']['DEMO']) / 60 / 60 / 24);
			$daysAgo = ($daysAgo > 0? $daysAgo: 1);
			$description .= '[B]'.Loc::getMessage('IMOL_NETWORK_DEMO').'[/B]: '.$daysAgo.'[BR]';
		}
		if (isset($params['USER']['GEO_DATA']) && !empty($params['USER']['GEO_DATA']))
		{
			$description .= '[B]'.Loc::getMessage('IMOL_NETWORK_GEO_DATA').'[/B]: '.$params['USER']['GEO_DATA'].'[BR]';
		}
		$description .= '[B]'.Loc::getMessage('IMOL_NETWORK_WWW').'[/B]: '.$params['USER']['PERSONAL_WWW'];
		$extraFields['EXTRA_URL'] = $params['USER']['PERSONAL_WWW'];

		$event = new \Bitrix\Main\Event('imconnector', 'OnReceivedMessage', Array(
			'user' => $userId,
			'connector' => 'network',
			'line' => $params['LINE_ID'],
			'chat' => Array('id' => $params['GUID'], 'description' => $description),
			'message' => $message,
			'extra' => $extraFields
		));
		$event->send();

		$connectorParameters = Array();
		if ($event->getResults())
		{
			foreach($event->getResults() as $evenResult)
			{
				$connectorParameters = $evenResult->getParameters();
				break;
			}
		}
		if (is_array($connectorParameters) && !empty($connectorParameters))
		{
			\Bitrix\ImBot\Service\Openlines::operatorMessageReceived(Array(
				'LINE_ID' => $params['LINE_ID'],
				'GUID' => $params['GUID'],
				'MESSAGE_ID' => $params['MESSAGE_ID'],
				'CONNECTOR_MID' => $connectorParameters['MESSAGE_ID'],
				'SESSION_ID' => $connectorParameters['SESSION_ID'],
			));
		}

		return true;
	}

	private function executeClientMessageUpdate($params)
	{
		\Bitrix\ImOpenLines\Log::write($params, 'NETWORK GET');

		$userId = $this->getUserId($params['USER']);

		$message = Array(
			'id' => $params['MESSAGE_ID'],
			'date' => "",
			'text' => $params['MESSAGE_TEXT']
		);

		$event = new \Bitrix\Main\Event('imconnector', 'OnReceivedMessageUpdate', Array(
			'user' => $userId,
			'connector' => 'network',
			'line' => $params['LINE_ID'],
			'chat' => Array('id' => $params['GUID']),
			'message' => $message
		));
		$event->send();

		return true;
	}

	private function executeClientMessageDelete($params)
	{
		\Bitrix\ImOpenLines\Log::write($params, 'NETWORK GET');

		$userId = $this->getUserId($params['USER']);

		$message = Array(
			'id' => $params['MESSAGE_ID']
		);

		$event = new \Bitrix\Main\Event('imconnector', 'OnReceivedMessageDel', Array(
			'user' => $userId,
			'connector' => 'network',
			'line' => $params['LINE_ID'],
			'chat' => Array('id' => $params['GUID']),
			'message' => $message
		));
		$event->send();

		return true;
	}

	private function executeClientMessageReceived($params)
	{
		\Bitrix\ImOpenLines\Log::write($params, 'NETWORK GET MESSAGE DELIVERED');

		if (!\Bitrix\Main\Loader::includeModule('im'))
		{
			return false;
		}

		$params['MESSAGE_ID'] = intval($params['MESSAGE_ID']);
		if ($params['MESSAGE_ID'] <= 0)
			return false;

		$messageData = \Bitrix\Im\Model\MessageTable::getList(Array(
			'select' => Array('CHAT_ENTITY_TYPE' => 'CHAT.ENTITY_TYPE', 'CHAT_ENTITY_ID' => 'CHAT.ENTITY_ID', 'CHAT_ID'),
			'filter' => array('=ID' => $params['MESSAGE_ID'])
		))->fetch();
		if (!$messageData || $messageData['CHAT_ENTITY_TYPE'] != 'LINES' || strpos($messageData['CHAT_ENTITY_ID'], 'network|'.$params['LINE_ID'].'|'.$params['GUID']) !== 0)
		{
			return false;
		}

		$messageParamData = \Bitrix\Im\Model\MessageParamTable::getList(Array(
			'select' => Array('PARAM_VALUE'),
			'filter' => array('=MESSAGE_ID' => $params['MESSAGE_ID'], '=PARAM_NAME' => 'SENDING')
		))->fetch();
		if (!$messageParamData || $messageParamData['PARAM_VALUE'] != 'Y')
		{
			return false;
		}

		$event = new \Bitrix\Main\Event('imconnector', 'OnReceivedStatusDelivery', Array(
			'connector' => 'network',
			'line' => $params['LINE_ID'],
			'chat' => Array('id' => $messageData['DIALOG_ID']),
			'im' => Array(
				'message_id' => $params['MESSAGE_ID'],
				'chat_id' => $messageData['CHAT_ID']
			),
			'message' => Array(
				'id' => Array($params['CONNECTOR_MID'])
			),
		));
		$event->send();

		return true;
	}




	public function search($text)
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		$result = \Bitrix\ImBot\Bot\Network::search($text);
		if (!$result)
		{
			$this->error = \Bitrix\ImBot\Bot\Network::getError();
		}
		return $result;
	}

	public function join($code)
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		$result = \Bitrix\ImBot\Bot\Network::join($code);
		if (!$result)
		{
			$this->error = \Bitrix\ImBot\Bot\Network::getError();
		}
		return $result;
	}

	public function registerConnector($lineId, $fields = array())
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		$result = \Bitrix\ImBot\Bot\Network::registerConnector($lineId, $fields);
		if (!$result)
		{
			$this->error = \Bitrix\ImBot\Bot\Network::getError();
		}
		return $result;
	}

	public function updateConnector($lineId, $fields)
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		$result = \Bitrix\ImBot\Bot\Network::updateConnector($lineId, $fields);
		if (!$result)
		{
			$this->error = \Bitrix\ImBot\Bot\Network::getError();
		}
		return $result;
	}

	public function unRegisterConnector($lineId)
	{
		if (!\Bitrix\Main\Loader::includeModule('imbot'))
		{
			$this->error = new Error(__METHOD__, 'IMBOT_ERROR', Loc::getMessage('IMOL_NETWORK_IMBOT_LOAD_ERROR'));
		}

		$result = \Bitrix\ImBot\Bot\Network::unRegisterConnector($lineId);
		if (!$result)
		{
			$this->error = \Bitrix\ImBot\Bot\Network::getError();
		}
		return $result;
	}

	private function getUserId($params, $createUser = true)
	{
		$orm = \Bitrix\Main\UserTable::getList(array(
			'select' => Array('ID', 'NAME', 'LAST_NAME', 'PERSONAL_GENDER', 'PERSONAL_WWW', 'EMAIL'),
			'filter' => array(
				'=EXTERNAL_AUTH_ID' => self::EXTERNAL_AUTH_ID,
				'=XML_ID' => 'network|'.$params['UUID']
			),
			'limit' => 1
		));

		$userId = 0;
		if($userFields = $orm->fetch())
		{
			$userId = $userFields['ID'];

			$updateFields = Array();
			if (!empty($params['NAME']) && $params['NAME'] != $userFields['NAME'])
			{
				$updateFields['NAME'] = $params['NAME'];
			}
			if (isset($params['LAST_NAME']) && $params['LAST_NAME'] != $userFields['LAST_NAME'])
			{
				$updateFields['LAST_NAME'] = $params['LAST_NAME'];
			}
			if (isset($params['PERSONAL_GENDER']) && $params['PERSONAL_GENDER'] != $userFields['PERSONAL_GENDER'])
			{
				$updateFields['PERSONAL_GENDER'] = $params['PERSONAL_GENDER'];
			}
			if (isset($params['PERSONAL_WWW']) && $params['PERSONAL_WWW'] != $userFields['PERSONAL_WWW'])
			{
				$updateFields['PERSONAL_WWW'] = $params['PERSONAL_WWW'];
			}
			if (isset($params['EMAIL']) && $params['EMAIL'] != $userFields['EMAIL'])
			{
				$updateFields['EMAIL'] = $params['EMAIL'];
			}

			if (!empty($updateFields))
			{
				$cUser = new \CUser;
				$cUser->Update($userId, $updateFields);
			}

		}
		else if ($createUser)
		{
			$userName = $params['NAME']? $params['NAME']: Loc::getMessage('IMOL_NETWORK_GUEST_NAME');
			$userLastName = $params['LAST_NAME'];
			$userGender = $params['PERSONAL_GENDER'];
			$userAvatar = $params['PERSONAL_PHOTO'];
			$userWww = $params['PERSONAL_WWW'];
			$userEmail = $params['EMAIL'];

			if ($userAvatar)
			{
				$userAvatar = \CFile::MakeFileArray($userAvatar);
			}

			$cUser = new \CUser;
			$fields['LOGIN'] = self::MODULE_ID . '_' . rand(1000,9999) . randString(5);
			$fields['NAME'] = $userName;
			$fields['LAST_NAME'] = $userLastName;
			if ($userAvatar)
			{
				$fields['PERSONAL_PHOTO'] = $userAvatar;
			}
			if ($userEmail)
			{
				$fields['EMAIL'] = $userEmail;
			}
			$fields['PERSONAL_GENDER'] = $userGender;
			$fields['PERSONAL_WWW'] = $userWww;
			$fields['PASSWORD'] = md5($fields['LOGIN'].'|'.rand(1000,9999).'|'.time());
			$fields['CONFIRM_PASSWORD'] = $fields['PASSWORD'];
			$fields['EXTERNAL_AUTH_ID'] = self::EXTERNAL_AUTH_ID;
			$fields['XML_ID'] =  'network|'.$params['UUID'];
			$fields['ACTIVE'] = 'Y';

			$userId = $cUser->Add($fields);
		}

		return $userId;
	}

	public static function getPublicLink($code)
	{
		if (!\Bitrix\Main\Loader::includeModule("socialservices"))
			return "";

		return \CSocServBitrix24Net::NETWORK_URL.'/oauth/select/?preset=im&IM_DIALOG=networkLines'.$code;
	}

	public function getError()
	{
		return $this->error;
	}
}