<?php

namespace Bitrix\Crm\Order;

use Bitrix\Sale;
use Bitrix\Main;

if (!Main\Loader::includeModule('sale'))
{
	return;
}

/**
 * Class PropertyTable
 * @package Bitrix\Crm\Order
 */
class UserProperties extends Sale\OrderUserProperties
{
}