<?php

namespace PT\MustUse\Blocks\CoreVideo;

use PT\MustUse\Package\Media as MediaPackage;

class Block
{
	public function run()
	{
		add_filter('render_block_core/embed', [$this, 'render']);
	}

	public function render($html)
	{

		if (empty($html)) {
			return $html;
		}

		$media_package = new MediaPackage();

		return $media_package->addHqParam($html);
	}
}
