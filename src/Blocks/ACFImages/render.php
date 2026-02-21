<?php

$images = get_field('images');
if (empty($images) || !is_array($images)) {
	return;
}

$image_ids = array_map(function ($image) {
	return $image['ID'];
}, $images);

// make sure $image_ids is an array of integers
$image_ids = array_map('intval', $image_ids);

// remove null or non-integer values
$image_ids = array_filter($image_ids, function ($id) {
	return is_int($id) && $id > 0;
});

if (empty($image_ids)) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php get_template_part('partials/grid500', false, ['post_ids' => $image_ids]); ?>
</div>