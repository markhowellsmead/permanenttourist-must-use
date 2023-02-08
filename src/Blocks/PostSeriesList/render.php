<?php

$terms = get_terms([
	'taxonomy' => 'post_tag',
	'hide_empty' => true,
	'meta_key' => ['tag_is_series'],
	'meta_value' => true,
]);

if (empty($terms)) {
	return;
}

$classNameBase = wp_get_block_default_classname($block->name);
$series = [];

foreach ($terms as $term) {

	$posts = get_posts([
		'tax_query' => [[
			'taxonomy' => 'post_tag',
			'field' => 'term_id',
			'terms' => $term->term_id
		]],
		'posts_per_page' => 1,
		'fields' => 'id'
	]);

	if (empty($posts)) {
		continue;
	}

	$post_date = get_the_date('YmdHis', $posts[0]->ID);

	$image = wp_get_attachment_image(get_term_meta($term->term_id, 'thumbnail', true), 'thumbnail', false, ['class' => "{$classNameBase}__image"]);

	if (!empty($image)) {
		$image = sprintf(
			'<figure class="%1$s__figure">%2$s</figure>',
			$classNameBase,
			$image
		);
	}

	$series["ts{$post_date}"] =
		sprintf('<div>%4$s <a href="%1$s">%2$s</a> (Most recent addition in %3$s)</div>', get_term_link($term), esc_html($term->name), get_the_date(get_option('date_format'), $posts[0]->ID), $image);
}

krsort($series);

foreach ($series as $series_entry) {
	echo $series_entry;
}
