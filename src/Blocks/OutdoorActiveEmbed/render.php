<?php

$outdooractive_url = get_post_meta(get_the_ID(), 'outdooractive_url', true);
//$outdooractive_url = "https://www.outdooractive.com/en/route/hiking-trail/holiday-region-interlaken/heiligenschwendi-reha-zentrum-to-chrindenhubel-and-back/325827395/";

if (empty($outdooractive_url) || filter_var($outdooractive_url, FILTER_VALIDATE_URL) === false) {
	return;
}

$title = $attributes['title'] ?? 'Outdooractive Route';

$classNameBase = 'wp-block-sht-outdoor-active-embed';


// Extract the last numeric part from the URL as track ID
$track_id = null;
if (preg_match('/(\d+)(?:\D*)$/', $outdooractive_url, $matches)) {
	$track_id = $matches[1];
}

if (!$track_id) {
	return;
}


?>
<div class="<?php echo $classNameBase; ?>">
	<?php if (!empty($title)): ?>
		<h2 class="<?php echo $classNameBase; ?>__title"><?php echo esc_html($title); ?></h2>
	<?php endif; ?>
	<figure class="<?php echo $classNameBase; ?>__figure">
		<figcaption class="<?php echo $classNameBase; ?>__caption wp-embed-caption">
			The embedded information in the map above is provided by <a href="https://www.outdooractive.com/" rel="noopener" target="_blank">Outdooractive</a>. You can read their cookie policy and change your privacy preferences for their content <a href="https://www.outdooractive.com/en/cookies.html" rel="noopener" target="_blank">here</a>.
		</figcaption>
		<div class="<?php echo $classNameBase; ?>__inner">
			<script src="https://www.outdooractive.com/en/embed/<?php echo $track_id; ?>/js?mw=false" defer data-deferred="1"></script>
		</div>
	</figure>
</div>