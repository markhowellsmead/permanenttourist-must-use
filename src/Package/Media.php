<?php

namespace PT\MustUse\Package;

use DOMDocument;
use DOMXPath;

/**
 * Everything to do with images, videos etc
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Media
{

	const META_KEY = 'video_ref';
	private $wide_aspectratio = 2;
	private $film_aspectratio = 1.7;
	private $xwide_aspectratio = 2.25;

	public function run()
	{
		add_filter('jpeg_quality', [$this, 'jpegQuality']);
		add_action('after_setup_theme', [$this, 'addImageSizes']);
		add_filter('image_size_names_choose', [$this, 'selectableImageSizes']);
		add_filter('body_class', [$this, 'thumbnailAspectCSS']);
		add_filter('post_class', [$this, 'postClasses']);
		add_action('wpseo_add_opengraph_images', [$this, 'videoThumbnail']);
		add_filter('wpseo_opengraph_image_size', [$this, 'yoastSeoOpengraphChangeImageSize'], 10, 0);
		add_filter('wp_get_loading_optimization_attributes', [$this, 'removeAsyncDecoding']);
	}

	public function addImageSizes()
	{
		// add_image_size('card', 296 * 2, 198 * 2, true);
		// add_image_size('photo_medium', 800 * 2, 9999);
		// add_image_size('list_view', 540 * 2, 9999);
		// add_image_size('medium', 720, 9999);
		// add_image_size('list_view_tall', 9999, 540);
		add_image_size('gutenberg_wide', 1280, 9999);
		// add_image_size('page', 1376, 9999);
		add_image_size('gutenberg_full', 2560, 9999);
		// add_image_size('full_uncropped', 2560, 9999, true);
		add_image_size('facebook_preview', 524 * 2, 273 * 2, true);
	}

	public function jpegQuality()
	{
		return 92;
	}

	public function selectableImageSizes($sizes)
	{
		$sizes['gutenberg_wide'] = __('Gutenberg wide', 'sht');
		$sizes['gutenberg_full'] = __('Gutenberg full', 'sht');
		return $sizes;
	}

	public function thumbnailAspectCSS($css_classes)
	{
		if (!has_post_thumbnail() || (bool) get_field('hide_thumbnail', get_the_ID())) {
			return $css_classes;
		}

		$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'post-thumbnail');

		if (is_array($image_src) && (int) ($image_src[1] ?? 0) && (int) ($image_src[2] ?? 0)) {
			$aspect = $image_src[1] / $image_src[2];
			if ($aspect >= $this->xwide_aspectratio) {
				$css_classes[] = 'o-body--xwidethumbnail';
			} elseif ($aspect >= $this->wide_aspectratio) {
				$css_classes[] = 'o-body--squarethumbnail';
			} elseif ($aspect == 1) {
				$css_classes[] = 'o-body--squarethumbnail';
			} elseif ($aspect < 1) {
				$css_classes[] = 'o-body--tallthumbnail';
			}
		}
		return $css_classes;
	}

	public function thumbnailAspect()
	{
		$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'post-thumbnail');
		if (is_array($image_src) && (int) ($image_src[1] ?? 0) && (int) ($image_src[2] ?? 0)) {
			$aspect = round((int) $image_src[1] / $image_src[2], 6);

			if ($aspect >= $this->xwide_aspectratio) {
				return 'xwide';
			} elseif ($aspect >= round($this->wide_aspectratio, 6)) {
				return 'wide';
			} elseif ($aspect >= round($this->film_aspectratio, 6)) {
				return '169';
			} elseif ($aspect == 1) {
				return 'square';
			} elseif ($aspect < 1) {
				return 'tall';
			}
			return 'landscape';
		}
		return null;
	}

	public function thumbnailSize($image_size)
	{
		$size = [];
		if (is_array($image_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $image_size))) {
			$size = [
				'width' => $image_src[1],
				'height' => $image_src[2]
			];
		}
		return $size;
	}

	public function postClasses($classes)
	{
		if (has_post_thumbnail()) {
			$classes[] = 'c-article__thumbnailaspect--' . $this->thumbnailAspect();
		} else {
			$classes[] = 'c-article--nothumbnail';
		}
		if (!empty(get_field('video_ref'))) {
			$classes[] = 'has-video-thumbnail';
		}
		return $classes;
	}

	/**
	 * Get remote video thumbnail URL
	 *
	 * @param  string $source_url The video URL
	 * @return string The Video Thumbnail URL
	 **/
	public static function getVideoThumbnail($source_url)
	{
		if ($source_url == '' || is_array($source_url)) {
			return '';
		}

		$atts = [
			'url' => $source_url
		];

		if (!is_string($atts['url'])) {
			return '';
		}

		$aPath = parse_url(trim($atts['url']));
		$aPath['host'] = str_replace('www.', '', $aPath['host']);

		if (empty($aPath['host'] ?? '') || empty($aPath['path'] ?? '')) {
			return '';
		}

		switch ($aPath['host']) {
			case 'youtu.be':
				$atts['id'] = preg_replace('~^/~', '', $aPath['path']);
				return 'https://i.ytimg.com/vi/' . $atts['id'] . '/hqdefault.jpg';
				break;

			case 'youtube.com':
				$aParams = explode('&', $aPath['query']);
				foreach ($aParams as $param) :
					$thisPair = explode('=', $param);
					if (strtolower($thisPair[0]) == 'v') :
						$atts['id'] = $thisPair[1];
						break;
					endif;
				endforeach;
				if (!isset($atts['id']) || !$atts['id']) {
					return '';
				} else {
					return 'https://i.ytimg.com/vi/' . $atts['id'] . '/hqdefault.jpg';
				}
				break;

			case 'vimeo.com':
				$urlParts = explode('/', $atts['url']);
				$hash = @unserialize(@file_get_contents('https://vimeo.com/api/v2/video/' . $urlParts[3] . '.php'));
				if ($hash && $hash[0] && (isset($hash[0]['thumbnail_large']) && $hash[0]['thumbnail_large'] !== '')) {
					return $hash[0]['thumbnail_large'];
				} else {
					return '';
				}
				break;

			default:
				return '';
				break;
		}
	}

	public function videoThumbnail($object)
	{
		if (!has_post_thumbnail() && !empty($video_url = get_post_meta(get_the_ID(), self::META_KEY, true))) {
			$video_thumbnail = $this->getVideoThumbnail($video_url);
			$object->add_image($video_thumbnail);
		}
	}

	public function yoastSeoOpengraphChangeImageSize()
	{
		return 'facebook_preview';
	}

	public function getCameraDescriptors($camera)
	{
		if (strpos($camera, 'iPhone') !== false) {
			return [
				'pre' => 'an',
				'camera' => $camera
			];
		}
		switch ($camera) {
			case 'Canon DIGITAL IXUS 500':
				return [
					'pre' => 'a',
					'camera' => 'Canon Ixus 500'
				];
				break;
			case 'K750i':
				return [
					'pre' => 'a',
					'camera' => 'Sony Ericsson K750i'
				];
				break;
			case 'Canon EOS 1200D':
				return [
					'pre' => 'a',
					'camera' => 'Canon EOS 1200D'
				];
				break;
			case 'NIKON D70':
				return [
					'pre' => 'a',
					'camera' => 'Nikon D70'
				];
				break;
			case 'NIKON D80':
				return [
					'pre' => 'a',
					'camera' => 'Nikon D80'
				];
				break;
			case 'NIKON D7000':
				return [
					'pre' => 'a',
					'camera' => 'Nikon D7000'
				];
				break;
			case 'FC2103':
				return [
					'pre' => 'a',
					'camera' => 'DJI Mavic Air'
				];
				break;
			case 'FC3170':
				return [
					'pre' => 'a',
					'camera' => 'DJI Mavic Air 2'
				];
				break;
			case 'FinePix X100':
				return [
					'pre' => 'a',
					'camera' => 'Fujifilm X100'
				];
				break;
			case 'X100V':
				return [
					'pre' => 'a',
					'camera' => 'Fujifilm X100V'
				];
				break;
			case 'X-T1':
				return [
					'pre' => 'a',
					'camera' => 'Fujifilm X-T1'
				];
				break;
			case 'X-T3':
				return [
					'pre' => 'a',
					'camera' => 'Fujifilm X-T3'
				];
				break;
		}
		return [];
	}

	public function convertShutterSpeed($speed)
	{
		if ((1 / $speed) > 1) {
			if ((number_format((1 / $speed), 1)) === 1.3
				|| number_format((1 / $speed), 1) === 1.5
				|| number_format((1 / $speed), 1) === 1.6
				|| number_format((1 / $speed), 1) === 2.5
			) {
				$pshutter = '1/' . number_format((1 / $speed), 1, '.', '') . 's';
			} else {
				$pshutter = '1/' . number_format((1 / $speed), 0, '.', '') . 's';
			}
		} else {
			$pshutter = $speed . 's';
		}

		return $pshutter;
	}

	/**
	 * Convert GPS DMS (degrees, minutes, seconds) to decimal format
	 * (longitude/latitude).
	 *
	 * @param int $deg Degrees
	 * @param int $min Minutes
	 * @param int $sec Seconds
	 *
	 * @return int The converted decimal-format value.
	 */
	public function DMStoDEC($deg, $min, $sec)
	{
		return $deg + ((($min * 60) + ($sec)) / 3600);
	}

	public function calculateGPS(array $image_meta)
	{

		if (!($image_meta['latitude'] ?? false) || !($image_meta['latitude'] ?? false)) {
			return [];
		}

		$gps = [];

		if (isset($image_meta['latitude'])) {
			$gps['lat']['deg'] = explode('/', $image_meta['latitude'][0]);
			$gps['lat']['deg'] = $gps['lat']['deg'][1] > 0 ? $gps['lat']['deg'][0] / $gps['lat']['deg'][1] : 0;
			$gps['lat']['min'] = explode('/', $image_meta['latitude'][1]);
			$gps['lat']['min'] = $gps['lat']['min'][1] > 0 ? $gps['lat']['min'][0] / $gps['lat']['min'][1] : 0;
			$gps['lat']['sec'] = explode('/', $image_meta['latitude'][2]);

			$lat_sec_0 = floatval($gps['lat']['sec'][0]);
			$lat_sec_1 = floatval($gps['lat']['sec'][1]);

			if ($lat_sec_0 > 0 && $lat_sec_1 > 0) {
				$gps['lat']['sec'] = $lat_sec_0 / $lat_sec_1;
			} else {
				$gps['lat']['sec'] = 0;
			}

			$gps['GPSLatitudeDecimal'] = $this->DMStoDEC($gps['lat']['deg'], $gps['lat']['min'], $gps['lat']['sec']);
			if (($image_meta['latitude_ref'] ?? false) === 'S') {
				$gps['GPSLatitudeDecimal'] = 0 - $gps['GPSLatitudeDecimal'];
			}
		} else {
			$gps['GPSLatitudeDecimal'] = null;
			$gps['GPSLatitudeRef'] = null;
		}

		if (isset($image_meta['longitude'])) {
			$gps['lon']['deg'] = explode('/', $image_meta['longitude'][0]);
			$gps['lon']['deg'] = $gps['lon']['deg'][1] > 0 ? $gps['lon']['deg'][0] / $gps['lon']['deg'][1] : 0;
			$gps['lon']['min'] = explode('/', $image_meta['longitude'][1]);
			$gps['lon']['min'] = $gps['lon']['min'][1] > 0 ? $gps['lon']['min'][0] / $gps['lon']['min'][1] : 0;
			$gps['lon']['sec'] = explode('/', $image_meta['longitude'][2]);

			$lon_sec_0 = floatval($gps['lon']['sec'][0]);
			$lon_sec_1 = floatval($gps['lon']['sec'][1]);

			if ($lon_sec_0 > 0 && $lon_sec_1 > 0) {
				$gps['lon']['sec'] = $lon_sec_0 / $lon_sec_1;
			} else {
				$gps['lon']['sec'] = 0;
			}

			$gps['GPSLongitudeDecimal'] = $this->DMStoDEC($gps['lon']['deg'], $gps['lon']['min'], $gps['lon']['sec']);
			if (($gps['longitude_ref'] ?? false) === 'W') {
				$gps['GPSLongitudeDecimal'] = 0 - $gps['GPSLongitudeDecimal'];
			}
		} else {
			$gps['GPSLongitudeDecimal'] = null;
			$gps['GPSLongitudeRef'] = null;
		}

		if ($gps['GPSLatitudeDecimal'] && $gps['GPSLongitudeDecimal']) {
			$gps['GPSCalculatedDecimal'] = $gps['GPSLatitudeDecimal'] . ',' . $gps['GPSLongitudeDecimal'];
		} else {
			$gps['GPSCalculatedDecimal'] = null;
		}

		return $gps;
	}

	/**
	 * Modifies the iframe src attribute to add the hq=1 parameter
	 *
	 * @param string $html
	 * @return string
	 */
	public function addHqParam(string $html): string
	{
		if (empty($html)) {
			return $html;
		}

		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$xpath = new DOMXPath($dom);
		$nodeList = $xpath->query('//iframe');

		foreach ($nodeList as $node) {
			$host = parse_url($node->getAttribute('src'), PHP_URL_HOST);
			if (strpos($host, 'youtube.com') === false && strpos($host, 'youtu.be') === false) {
				continue;
			}

			$new_src = add_query_arg('hq', '1', $node->getAttribute('src'));
			$node->setAttribute('src', $new_src);
		}

		$body = $dom->saveHtml($dom->getElementsByTagName('body')->item(0));
		return str_replace(['<body>', '</body>'], '', $body);
	}

	/**
	 * Modify the loading attribute of images to be synchronous
	 * This avoids an intermittent issue where the image is not lazy-loaded
	 *
	 * @param array $loading_attrs
	 * @return array
	 */
	public function removeAsyncDecoding($loading_attrs)
	{
		$loading_attrs['decoding'] = 'sync';
		return $loading_attrs;
	}
}
