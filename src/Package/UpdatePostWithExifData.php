<?php

namespace PT\MustUse\Package;

class UpdatePostWithExifData
{
	public $post_thumbnail = null;
	public $exif = null;

	public function run()
	{
		add_action('post_submitbox_misc_actions', [$this, 'addCheckboxes']);
		add_action('save_post', [$this, 'addImageLocationToPost'], 10, 3);
		add_action('save_post', [$this, 'addImageTagsToPost'], 10, 3);
		add_filter('update-post-with-exif-data/post_taxonomy', [$this, 'customiseTaxonomy']);
	}

	/**
	 * Add checkboxes to the Post submit box in wp-admin.
	 */
	public function addCheckboxes()
	{
		echo '<div class="misc-pub-section" style="border-top: 1px solid #eee">
			<label><input type="checkbox" value="1" name="update-post-with-exif-data[update_tags_from_exif]" /> Update tags from EXIF</label>
		</div>';
		echo '<div class="misc-pub-section">
			<label><input type="checkbox" value="1" name="update-post-with-exif-data[update_location_from_exif]" /> Update post location from EXIF</label>
		</div>';
	}

	//////////////////////////////////////////////////

	/**
	 * Save thumbnail exif location to post when a post is saved.
	 *
	 * @param int  $post_id The ID of the post.
	 * @param post $post    the post.
	 */
	public function addImageLocationToPost($post_id, $post = null, $update = false)
	{
		if (isset($_POST['update-post-with-exif-data']) && isset($_POST['update-post-with-exif-data']['update_location_from_exif'])) {

			if (!$post) {
				global $post;
			}

			$this->getPostThumbnailExif();

			if (isset($this->exif['GPS']) && isset($this->exif['GPS']['GPSCalculatedDecimal'])) {
				$location_data = array(
					'address' => $this->exif['GPS']['GPSCalculatedDecimal'],
					'lat' => $this->exif['GPS']['GPSLatitudeDecimal'],
					'lng' => $this->exif['GPS']['GPSLongitudeDecimal'],
					'city' => $this->exif['GPS']['iptc']['city'],
					'state' => $this->exif['GPS']['iptc']['state'],
					'country' => $this->exif['GPS']['iptc']['country'],
				);

				$this->addOrUpdateMeta($post->ID, 'location', serialize($location_data));
			} else {
				do_action('update-post-with-exif-data/no-gps', $post_id, $this->exif);
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Save thumbnail IPTC keywords to the post as post_tag taxonomy entries.
	 *
	 * @param int  $post_id The ID of the post.
	 * @param post $post    the post.
	 */
	public function addImageTagsToPost($post_id, $post, $update)
	{
		if (isset($_POST['update-post-with-exif-data']) && isset($_POST['update-post-with-exif-data']['update_tags_from_exif'])) {
			$this->getPostThumbnailExif();

			if (isset($this->exif['GPS']) && isset($this->exif['GPS']['GPSCalculatedDecimal'])) {

				$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($this->exif['GPS']['GPSCalculatedDecimal']) . '&sensor=false';

				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_ENCODING, '');
				$curlData = curl_exec($curl);
				curl_close($curl);

				$address = json_decode($curlData, 1);

				if (is_array($address) && isset($address['results']) && count($address['results']) && isset($address['results'][0]['address_components'])) {
					$address_tags = [];

					foreach ($address['results'][0]['address_components'] as $addresscomponent) {
						foreach ($addresscomponent as $sub_addresscomponent) {
							if (
								in_array('locality', $addresscomponent['types'])
								|| in_array('administrative_area_level_2', $addresscomponent['types'])
								|| in_array('administrative_area_level_1', $addresscomponent['types'])
								|| in_array('transit_station', $addresscomponent['types'])
								|| in_array('establishment', $addresscomponent['types'])
								|| in_array('country', $addresscomponent['types'])
							) {
								if (!in_array($addresscomponent['long_name'], $address_tags)) {
									$address_tags[] = $addresscomponent['long_name'];
								}
							}
						}
					}
				}

				if (isset($this->exif['GPS']['iptc']) && isset($this->exif['GPS']['iptc']['keywords'])) {
					$post_taxonomy = apply_filters('update-post-with-exif-data/post_taxonomy', 'post_tag');

					wp_set_object_terms($post_id, $this->exif['GPS']['iptc']['keywords'], $post_taxonomy, true);

					if (!empty($address_tags)) {
						wp_set_object_terms($post_id, $address_tags, $post_taxonomy, true);
					}
				}
			} else {
				do_action('update-post-with-exif-data/no-gps', $post_id, $this->exif);
			}
		}
	}

	//////////////////////////////////////////////////

	public function getPostThumbnailExif($postID = 0)
	{
		global $post;

		if ($post && $post->ID) {
			switch ($post->post_type) {
				case 'attachment':
					$this->exif = exif_read_data($post->guid, 0, true);

					$gps = $this->exif['GPS'];

					if (isset($gps['GPSLongitude']) && isset($gps['GPSLatitude'])) {
						$this->extendPostThumbnailExif();
					}
					break;

				default:
					$thumbnailID = get_post_thumbnail_id($post->ID);
					if ($thumbnailID) {
						$metaData = wp_get_attachment_metadata($thumbnailID);
						$paths = wp_upload_dir();
						$pre = $paths['basedir'];

						$this->post_thumbnail = $pre . '/' . $metaData['file'];

						$this->exif = @exif_read_data($this->post_thumbnail, 0, true);

						if ($this->exif && is_array($this->exif['GPS']) && isset($this->exif['GPS']['GPSLongitude']) && isset($this->exif['GPS']['GPSLatitude'])) {
							$this->extendPostThumbnailExif();
						}
					}
					break;
			}
		}
	}

	//////////////////////////////////////////////////

	public function addOrUpdateMeta($postID, $field, $value)
	{
		//	if meta already exists in db for $post then update it, else create it
		//	return true or false
		return update_post_meta($postID, $field, $value) || add_post_meta($postID, $field, $value);
	}

	//////////////////////////////////////////////////

	public function DMStoDEC($deg, $min, $sec)
	{
		// Converts DMS ( Degrees / minutes / seconds )
		// to decimal format longitude / latitude
		return $deg + ((($min * 60) + ($sec)) / 3600);
	}

	//////////////////////////////////////////////////

	public function DECtoDMS($dec)
	{
		// Converts decimal longitude / latitude to DMS
		// ( Degrees / minutes / seconds )

		// This piece of code may appear to be inefficient, but to avoid issues with floating
		// point math, we extract the integer part and the float part by using a string function.

		$vars = explode('.', $dec);
		$deg = $vars[0];
		$tempma = '0.' . $vars[1];

		$tempma = $tempma * 3600;
		$min = floor($tempma / 60);
		$sec = $tempma - ($min * 60);

		return array('deg' => $deg, 'min' => $min, 'sec' => $sec);
	}

	//////////////////////////////////////////////////

	private function extendPostThumbnailExif()
	{
		$exifdata = &$this->exif['GPS'];

		/*
        [GPSLatitudeRef] => N
        [GPSLatitude] => Array
        (
            [0] => 57/1
            [1] => 31/1
            [2] => 21334/521
        )

        [GPSLongitudeRef] => W
        [GPSLongitude] => Array
        (
            [0] => 4/1
            [1] => 16/1
            [2] => 27387/1352
        )
        */

		$GPS = array();

		$GPS['lat']['deg'] = explode('/', $exifdata['GPSLatitude'][0]);
		$GPS['lat']['deg'] = $GPS['lat']['deg'][0] / $GPS['lat']['deg'][1];
		$GPS['lat']['min'] = explode('/', $exifdata['GPSLatitude'][1]);
		$GPS['lat']['min'] = $GPS['lat']['min'][0] / $GPS['lat']['min'][1];
		$GPS['lat']['sec'] = explode('/', $exifdata['GPSLatitude'][2]);
		$GPS['lat']['sec'] = floatval($GPS['lat']['sec'][0]) / floatval($GPS['lat']['sec'][1]);

		$exifdata['GPSLatitudeDecimal'] = $this->DMStoDEC($GPS['lat']['deg'], $GPS['lat']['min'], $GPS['lat']['sec']);
		if ($exifdata['GPSLatitudeRef'] == 'S') {
			$exifdata['GPSLatitudeDecimal'] = 0 - $exifdata['GPSLatitudeDecimal'];
		}

		$GPS['lon']['deg'] = explode('/', $exifdata['GPSLongitude'][0]);
		$GPS['lon']['deg'] = $GPS['lon']['deg'][0] / $GPS['lon']['deg'][1];
		$GPS['lon']['min'] = explode('/', $exifdata['GPSLongitude'][1]);
		$GPS['lon']['min'] = $GPS['lon']['min'][0] / $GPS['lon']['min'][1];
		$GPS['lon']['sec'] = explode('/', $exifdata['GPSLongitude'][2]);
		$GPS['lon']['sec'] = floatval($GPS['lon']['sec'][0]) / floatval($GPS['lon']['sec'][1]);

		$exifdata['GPSLongitudeDecimal'] = $this->DMStoDEC($GPS['lon']['deg'], $GPS['lon']['min'], $GPS['lon']['sec']);
		if ($exifdata['GPSLongitudeRef'] == 'W') {
			$exifdata['GPSLongitudeDecimal'] = 0 - $exifdata['GPSLongitudeDecimal'];
		}

		$exifdata['GPSCalculatedDecimal'] = $exifdata['GPSLatitudeDecimal'] . ',' . $exifdata['GPSLongitudeDecimal'];
		//$exifdata['googlemaps_image'] = '<img src="http://maps.googleapis.com/maps/api/staticmap?center='.$exifdata['GPSCalculatedDecimal'].'&zoom=14&size=980x540&maptype=hybrid&markers=color:red|'.$exifdata['GPSCalculatedDecimal'].'&sensor=false" alt="google map">';

		$size = getimagesize($this->post_thumbnail, $info);
		if (isset($info['APP13'])) {
			$iptc = iptcparse($info['APP13']);

			if (is_array($iptc)) {
				$exifdata['iptc']['caption'] = isset($iptc['2#120']) ? $iptc['2#120'][0] : '';
				$exifdata['iptc']['graphic_name'] = isset($iptc['2#005']) ? $iptc['2#005'][0] : '';
				$exifdata['iptc']['urgency'] = isset($iptc['2#010']) ? $iptc['2#010'][0] : '';
				$exifdata['iptc']['category'] = @$iptc['2#015'][0];

				// note that sometimes supp_categories contans multiple entries
				$exifdata['iptc']['supp_categories'] = @$iptc['2#020'][0];
				$exifdata['iptc']['spec_instr'] = @$iptc['2#040'][0];
				$exifdata['iptc']['creation_date'] = @$iptc['2#055'][0];
				$exifdata['iptc']['photog'] = @$iptc['2#080'][0];
				$exifdata['iptc']['credit_byline_title'] = @$iptc['2#085'][0];
				$exifdata['iptc']['city'] = @$iptc['2#090'][0];
				$exifdata['iptc']['state'] = @$iptc['2#095'][0];
				$exifdata['iptc']['country'] = @$iptc['2#101'][0];
				$exifdata['iptc']['otr'] = @$iptc['2#103'][0];
				$exifdata['iptc']['headline'] = @$iptc['2#105'][0];
				$exifdata['iptc']['source'] = @$iptc['2#110'][0];
				$exifdata['iptc']['photo_source'] = @$iptc['2#115'][0];
				$exifdata['iptc']['caption'] = @$iptc['2#120'][0];

				$exifdata['iptc']['keywords'] = @$iptc['2#025'];
			}
		}
	}

	public function customiseTaxonomy($taxonomy)
	{
		global $post;

		if ($post->post_type === 'photo') {
			return 'collection';
		} else if ($post->post_type === 'viewpoints') {
			return 'viewpoints_keyword';
		}

		return $taxonomy;
	}
}
