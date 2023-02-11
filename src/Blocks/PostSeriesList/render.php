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

	$permalink = get_term_link($term);

	$post_date = get_the_date('YmdHis', $posts[0]->ID);

	$image = wp_get_attachment_image(get_term_meta($term->term_id, 'thumbnail', true), 'medium', false, ['class' => "{$classNameBase}__image"]);

	if (!empty($image)) {
		$image = sprintf(
			'<figure class="%1$s__figure"><a href="%2$s">%3$s</a></figure>',
			$classNameBase,
			$permalink,
			$image
		);
	}

	$description = '';

	if (!empty($term->description)) {
		$description = sprintf('<div class="%1$s__excerpt-content">%2$s</div>', $classNameBase, $term->description);
	}

	ob_start();
?>
	<li class="<?php echo $classNameBase; ?>__entry">
		<div class="is-layout-constrained ___wp-container-11 wp-block-group">
			<?php if (!empty($image)) { ?>
				<div class="wp-block-sht-primary-media">
					<?php echo $image; ?>
				</div>
			<?php } else { ?>
				<div class="wp-block-sht-primary-media wp-block-sht-primary-media--empty"></div>
			<?php } ?>

			<div class="is-layout-constrained ___wp-container-10 wp-block-group <?php echo $classNameBase; ?>__content">
				<h2 class="wp-block-post-title has-large-font-size"><a href="<?php echo $permalink; ?>" target="_self"><?php echo esc_html($term->name); ?></a></h2>
				<div class="is-layout-flow ___wp-container-9 wp-block-group post-meta has-small-font-size">
					<div class="wp-block-post-date">Most recent addition: <time><?php echo get_the_date(get_option('date_format'), $posts[0]->ID); ?></time></div>
				</div>

				<?php if (!empty($description)) { ?>
					<div class="<?php echo $classNameBase; ?>__excerpt has-smaller-font-size h-stack">
						<?php echo $description; ?>
						<p class="wp-block-post-excerpt__more-text"><a class="wp-block-post-excerpt__more-link" href="<?php echo $permalink; ?>">Read more</a></p>
					</div>
				<?php } ?>
			</div>
		</div>
	</li>
<?php

	//printf('<div>%4$s %5$s <a href="%1$s">%2$s</a> (Most recent addition in %3$s)</div>', $permalink, esc_html($term->name), get_the_date(get_option('date_format'), $posts[0]->ID), $image, $description);
	$html = ob_get_contents();
	ob_end_clean();
	$series["ts{$post_date}"] = $html;
}

krsort($series);

?>
<div class="<?php echo $classNameBase; ?> is-layout-flow  alignwide">
	<ul class="is-layout-flow is-flex-container columns-3 <?php echo $classNameBase; ?>__entries">
		<?php
		foreach ($series as $series_entry) {
			echo $series_entry;
		}
		?>
	</ul>
</div>