<?php

namespace PT\MustUse\Package;

class MapFromMeta
{

	private $post_types = array('photo');
	private $atts = array();
	private $postID = 0;
	private $output = '';

	public function __construct()
	{
		$this->post_types = apply_filters('MapFromMeta:post_types', $this->post_types);
		add_shortcode('mapfrommeta', array($this, 'shortcode'));
	}

	public function dump($var, $die = false)
	{
		echo '<pre>' . print_r($var, 1) . '</pre>';
		if ($die) {
			die();
		}
	}

	public function shortcode($atts)
	{

		$this->atts = shortcode_atts(array(
			'postID' => '',
		), $atts);

		return $this->generate();
	}

	public function generate()
	{

		$this->postID = intval($this->atts['postID']);

		if (!$this->postID) {
			global $post;
			$this->postID = $post->ID;
		}

		$postLocation = get_post_meta($this->postID, 'location', true);

		if (empty($postLocation)) {

			$this->locationFromPostThumbnailMeta();
		}

		return $this->output;
	}

	public function locationFromPostThumbnailMeta()
	{

		if (has_post_thumbnail($this->postID)) {

			$thumbnailID = get_post_thumbnail_id($this->postID);

			if ($thumbnailID) {

				$meta = wp_get_attachment_metadata($thumbnailID);

				if (is_array($meta['image_meta']) && is_array($meta['image_meta']['latitude']) && is_array($meta['image_meta']['longitude'])) {

					require_once 'ParseGPS.php';

					$parser = new ParseGPS(array(
						'latitude_ref' => $meta['image_meta']['latitude_ref'],
						'latitude' => $meta['image_meta']['latitude'],
						'longitude_ref' => $meta['image_meta']['longitude_ref'],
						'longitude' => $meta['image_meta']['longitude']
					));

					$parser->setMapSize('1000x500');

					$data = $parser->getData();

					if (isset($data['googlemaps_decimal'])) {
						$this->output = $data['googlemaps_decimal'];
					}
				}
			}
		}
	}
}
