<?php

namespace Bitrix\Main\UI\Viewer\Renderer;

use Bitrix\Main\Engine\Response\ResizedImage;

class Image extends Renderer
{
	const WIDTH  = 1920;
	const HEIGHT = 1080;

	const JS_TYPE_IMAGE = 'image';

	public function getWidth()
	{
		return $this->getOption('width', self::WIDTH);
	}

	public function getHeight()
	{
		return $this->getOption('height', self::HEIGHT);
	}

	public function getOriginalImage()
	{
		return $this->getOption('originalImage');
	}

	public static function getJsType()
	{
		return self::JS_TYPE_IMAGE;
	}

	public static function getAllowedContentTypes()
	{
		return [
			'image/gif',
			'image/jpeg',
			'image/jpeg',
			'image/bmp',
			'image/png',
		];
	}

	public function render()
	{
		$imageFile = $this->getOriginalImage();
		if (!$imageFile)
		{
			return null;
		}

		$resizedImage = new ResizedImage($imageFile, $this->getWidth(), $this->getHeight());
		$resizedImage->setResizeType(BX_RESIZE_IMAGE_PROPORTIONAL);

		return $resizedImage;
	}
}