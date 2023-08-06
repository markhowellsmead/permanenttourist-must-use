<?php

use PT\MustUse\Package\Media as MediaPackage;

if (is_singular('post') && get_page_template_slug() === 'single-no-thumbnail') {
	return '';
}

$post_id = get_the_ID();
$classNameBase = wp_get_block_default_classname($block->name);
$align = $attributes['align'] ?? '';

$className = $attributes['className'] ?? '';

$media_size = 'medium';
switch ($align) {
	case 'wide':
		$media_size = 'gutenberg_wide';
		break;
	case 'full':
		$media_size = 'full';
		break;
}

$content = '';

if (!empty($video_url = get_field('video_ref', $post_id))) {

	if (is_singular('post') || is_singular('page') && !$attributes['hideInlineEmbed'] ?? false) {

		$url_parts = parse_url($video_url, PHP_URL_QUERY);

		if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
			$video_url = add_query_arg('hq', '1', $video_url);
		}

		$video_player = wp_oembed_get($video_url);

		if (empty($video_player)) {
			return $video_player;
		}

		$media_package = new MediaPackage();

		$video_player = $media_package->addHqParam($video_player);

		$content = sprintf(
			'<figure class="%1$s %2$s__figure %3$s__figure--video">%2$s</figure>',
			$className,
			$classNameBase,
			$video_player,
		);
	} else {

		$thumbnail = pt_must_use_get_instance()->Package->Media->getVideoThumbnail($video_url);

		if (!empty($thumbnail)) {
			$content = sprintf(
				'<figure class="%1$s %2$s__figure %2$s__figure--%3$s"><a href="%6$s"><img class="%2$s__image" src="%4$s" alt="%5$s" /></a></figure>',
				$className,
				$classNameBase,
				$media_size,
				pt_must_use_get_instance()->Package->Media->getVideoThumbnail($video_url),
				get_the_title($post_id),
				get_the_permalink($post_id)
			);
		}
	}
} elseif (has_post_thumbnail($post_id)) {
	$image = wp_get_attachment_image(get_post_thumbnail_id($post_id), $media_size, false, ['class' => "{$classNameBase}__image"]);

	if (!empty($image)) {

		// Weirdness when placing this block in a post list on a page
		// This ensures the correct link being set
		if (is_singular(get_post_type(get_the_ID()))) {
			$content = sprintf('<figure class="%1$s %2$s__figure %2$s__figure--%3$s">%4$s</figure>', $className, $classNameBase, $media_size, $image);
		} else {
			$content = sprintf('<figure class="%1$s %2$s__figure %2$s__figure--%3$s"><a href="%5$s" title="%6$s">%4$s</a></figure>', $className, $classNameBase, $media_size, $image, get_the_permalink($post_id), 'Article: ' . get_the_title($post_id));
		}
	}
}

if (!is_singular('post') && empty($content)) {
	return sprintf('<div aria-hidden class="%1$s %2$s__figure %2$s__figure--empty"><a href="%3$s">%4$s</a></div>', $className, $classNameBase, get_the_permalink($post_id), get_the_title($post_id));
}

if (!empty($align)) {
	$align = " align{$align}";
}

?>

<div class="<?php echo $className . ' ' . $classNameBase . $align; ?>">
	<?php echo $content; ?>
</div>
