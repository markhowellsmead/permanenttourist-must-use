<?php

use PT\MustUse\Package\Media as MediaPackage;

if (is_singular('post') && get_page_template_slug() === 'single-no-thumbnail') {
	return '';
}

$post_id = get_the_ID();
$classNameBase = wp_get_block_default_classname($block->name);
$align = $attributes['align'] ?? '';

$className = $attributes['className'] ?? '';

if (!empty($className)) {
	$className = "{$className} ";
}

$media_size = 'medium';
switch ($align) {
	case 'wide':
		$media_size = 'gutenberg_wide';
		break;
	case 'full':
		$media_size = 'full';
		break;
}

if (!empty($attributes['resolution'])) {
	$media_size = $attributes['resolution'];
}

$content = '';
$media_package = new MediaPackage();

if (!empty($video_url = get_post_meta($post_id, 'video_ref', true))) {

	if (is_singular('post') || is_singular('page') && !($attributes['hideInlineEmbed'] ?? false)) {

		$url_parts = parse_url($video_url, PHP_URL_QUERY);

		$video_player = wp_oembed_get($video_url);

		if (empty($video_player)) {
			return $video_player;
		}

		$video_player = $media_package->addHqParam($video_player);
		$video_id = $media_package->getYoutubeId($video_url);

		//$thumbnail = $media_package->getVideoThumbnail($video_url, 'maxresdefault');
		$domain = $_SERVER['HTTP_HOST'];
		$thumbnail = "https://{$domain}/youtube-thumbnail.php?id={$video_id}";

		if (!empty($thumbnail)) {
			$thumbnail = sprintf(
				'<link itemprop="thumbnailUrl" href="%1$s"><meta itemprop="thumbnail" content="%1$s"><meta itemprop="name" content="%3$s"><img src="%1$s" class="%2$s__image" alt="%3$s" /><button class="shp-video-play-button" aria-label="Play video">Play video</button>',
				$thumbnail,
				$classNameBase,
				get_the_title($post_id)
			);

			$thumbnail = sprintf('<figure class="%1$s%2$s__figure %2$s__figure--video shp-video-play-button-wrapper">%3$s</figure>', $className, $classNameBase, $thumbnail);
		}

		$template_figure = sprintf(
			'<figure class="%1$s%2$s__figure %2$s__figure--video">%3$s</figure>',
			$className,
			$classNameBase,
			function_exists('hd_bcve_allowed_innerblock_html') ? wp_kses($video_player, hd_bcve_allowed_innerblock_html()) : $video_player
		);

		$content = sprintf(
			'%1$s<template>%2$s</template>',
			$thumbnail,
			$template_figure
		);
	} else {

		$thumbnail = $media_package->getVideoThumbnail($video_url);

		if (!empty($thumbnail)) {
			$content = sprintf(
				'<figure class="%1$s%2$s__figure %2$s__figure--%3$s"><a href="%6$s"><img class="%2$s__image" src="%4$s" alt="%5$s" /></a></figure>',
				$className,
				$classNameBase,
				$media_size,
				$media_package->getVideoThumbnail($video_url),
				get_the_title($post_id),
				get_the_permalink($post_id)
			);
		}
	}
} elseif (has_post_thumbnail($post_id)) {

	$thumbnail_id = get_post_thumbnail_id($post_id);
	$image = wp_get_attachment_image_url($thumbnail_id, $media_size);
	$alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

	if (!empty($image)) {

		$image = sprintf(
			'<img loading="lazy" decode="sync" class="%1$s__image" src="%2$s" alt="%3$s" />',
			$classNameBase,
			$image,
			$alt
		);

		// Weirdness when placing this block in a post list on a page
		// This ensures the correct link being set
		if (is_singular(get_post_type(get_the_ID()))) {
			$content = sprintf(
				'<figure class="%1$s%2$s__figure %2$s__figure--%3$s">%4$s</figure>',
				$className,
				$classNameBase,
				$media_size,
				$image
			);
		} else {
			$content = sprintf(
				'<figure class="%1$s%2$s__figure %2$s__figure--%3$s"><a href="%5$s" title="%6$s">%4$s</a></figure>',
				$className,
				$classNameBase,
				$media_size,
				$image,
				get_the_permalink($post_id),
				'Article: ' . get_the_title($post_id)
			);
		}
	}
}

if (!is_singular('post') && empty($content)) {
	return sprintf(
		'<div aria-hidden class="%1$s%2$s__figure %2$s__figure--empty"><a href="%3$s">%4$s</a></div>',
		$className,
		$classNameBase,
		get_the_permalink($post_id),
		get_the_title($post_id)
	);
}

if (!empty($align)) {
	$align = " align{$align}";
}

if (empty($content)) {
	return;
}

?>

<div class="<?php echo $className . ' ' . $classNameBase . $align; ?>">
	<?php echo $content; ?>
</div>