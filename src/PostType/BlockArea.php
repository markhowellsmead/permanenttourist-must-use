<?php

namespace PT\MustUse\PostType;

/**
 * Block Area post type
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class BlockArea
{

	public function run()
	{
		add_filter('register_post_type_args', [$this, 'customPostTypeArgs'], 10, 2);
	}

	public function customPostTypeArgs($args, $post_type)
	{
		if ($post_type == 'block_area') {
			$args['publicly_queryable'] = false;
		}
		return $args;
	}
}
