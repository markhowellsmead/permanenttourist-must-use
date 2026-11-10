<?php

namespace PT\MustUse\Blocks\PageTeaserCover;

// "inherit" follows the surrounding query (Query Loop, Advanced Query Loop).
// A post chosen explicitly in the block takes precedence over the context.
$postType = $attributes['postType'] ?? 'inherit';
$postId = (int) ($attributes['postId'] ?? 0);

if (!$postId) {
	$contextPostId = (int) ($block->context['postId'] ?? 0);
	$contextPostType = $block->context['postType'] ?? get_post_type($contextPostId);

	if ($contextPostId && ($postType === 'inherit' || $postType === $contextPostType)) {
		$postId = $contextPostId;
	}
}

if (!$postId) {
	return '';
}

$imageSize = $attributes['imageSize'];
$title = esc_html(get_the_title($postId));
$excerpt = get_the_excerpt($postId);
$link = get_permalink($postId);
$linkText = !empty($attributes['linkText']) ? esc_html($attributes['linkText']) : $title;
$classNameBase = wp_get_block_default_classname($block->name);

$outerStyles = Block::outerStylesCalcString($attributes);

$objectPosition = 'center center';
$focalPoint = $attributes['focalPoint'] ?? null;
if ($focalPoint) {
	$x = $focalPoint['x'] * 100;
	$y = $focalPoint['y'] * 100;
	$objectPosition = "{$x}% {$y}%";
}

$outerClasses = [];

$innerConstraint = (bool) ($attributes['innerConstraint'] ?? false);

if ($innerConstraint) {
	$outerClasses[] = "is-style-inner-constraint";
}

$innerStyles = Block::innerStylesCalcString($attributes['style'] ?? []);
if (!empty($innerStyles)) {
	$innerStyles = 'style="' . $innerStyles . '"';
}

$image = wp_get_attachment_image(get_post_thumbnail_id($postId), $imageSize, false, ['class' => "{$classNameBase}__image", 'style' => "object-position: {$objectPosition};"]);

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

<div <?php echo get_block_wrapper_attributes(['style' => $outerStyles, 'class' => implode(' ', $outerClasses)]); ?>>
	<div class="<?php echo $classNameBase; ?>__inner" <?php echo $innerStyles; ?>>
		<div class="<?php echo $classNameBase; ?>__content with--text-shadow">
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

		<a href="<?php echo $link; ?>" class="<?php echo $classNameBase; ?>__link--flood"><?php echo $linkText; ?></a>

	</div>
</div>