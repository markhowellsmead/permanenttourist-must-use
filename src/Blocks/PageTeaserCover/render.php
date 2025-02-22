<?php

namespace PT\MustUse\Blocks\PageTeaserCover;

$postId = (int) ($attributes['postId'] ?? null);

if (!$postId) {
	return '';
}




$imageSize = $attributes['imageSize'];
$title = esc_html(get_the_title($postId));
$excerpt = get_the_excerpt($postId);
$link = get_permalink($postId);
$linkText = esc_html($attributes['linkText'] ?? $title);
$classNameBase = wp_get_block_default_classname($block->name);

$contentStyles = Block::contentStylesCalcString($attributes['style']);

if (!empty($contentStyles)) {
	$contentStyles = 'style="' . $contentStyles . '"';
}

$innerStyles = Block::innerStylesCalcString($attributes['style']);
if (!empty($innerStyles)) {
	$innerStyles = 'style="' . $innerStyles . '"';
}

$image = wp_get_attachment_image(get_post_thumbnail_id($postId), $imageSize, false, ['class' => "{$classNameBase}__image"]);

if (!empty($image)) {
	$image = sprintf(
		'<figure class="%1$s__figure">%2$s</figure>',
		$classNameBase,
		$image
	);
} else {
	$image = sprintf(
		'<figure class="%1$s__figure %1$s__figure--empty"></figure>',
		$classNameBase
	);
}

?>

<div <?php echo get_block_wrapper_attributes(); ?>>
	<div class="<?php echo $classNameBase; ?>__inner" <?php echo $innerStyles; ?>>
		<div class="<?php echo $classNameBase; ?>__content" <?php echo $contentStyles; ?>>
			<h2 class="<?php echo $classNameBase; ?>__title">
				<?php echo $title; ?>
			</h2>

			<?php if (!empty($excerpt)) { ?>
				<div class="<?php echo $classNameBase; ?>__excerpt">
					<?php echo $excerpt; ?>
				</div>
			<?php } ?>

			<div class="<?php echo $classNameBase; ?>__link-wrapper wp-block-button is-style-with-arrow-right has-smaller-font-size">
				<a href="<?php echo $link; ?>" class="<?php echo $classNameBase; ?>__link wp-block-button__link has-transparent-background-color has-background wp-element-button">
					<span class="<?php echo $classNameBase; ?>__link-text"><?php echo $linkText; ?></span>
				</a>
			</div>
		</div>

		<?php echo $image; ?>

		<a href=" <?php echo $link; ?>" class="<?php echo $classNameBase; ?>__link--flood"><?php echo $linkText; ?></a>

	</div>
</div>
