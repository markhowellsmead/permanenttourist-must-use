<?php

namespace PT\MustUse\PostType;

use stdClass;

/**
 * Photo post type
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Photo
{

	private $post_type = 'photo';

	public function run()
	{
		add_action('init', [$this, 'registerPostType']);
		add_action('init', [$this, 'registerCustomTaxonomies'], 0);
		add_action('init', [$this, 'addCapabilities']);
		add_filter('get_the_archive_title', [$this, 'changeTheTitle'], 30);
		add_action('pre_get_posts', [$this, 'postsPerAlbumPage']);
		add_action('pre_get_posts', [$this, 'postsPerPage'], 10, 1);
		add_shortcode('pt-photo', [$this, 'shortcode'], 10, 1);
		add_shortcode('photo_post_id', [$this, 'shortcodePostID'], 10, 0);
		add_action('template_redirect', [$this, 'noPosts404']);
		add_filter('rewrite_rules_array', [$this, 'archiveRewriteRules']);

		add_filter('permanenttourist-v10/post_meta_information', [$this, 'postThumbnailMeta'], 10, 2);
		add_filter('sherborne_road/post_meta_information', [$this, 'postThumbnailMeta'], 10, 2);
		add_action('mhm-attachment-from-ftp-publish/post_created', [$this, 'changePostSlug'], 10, 1);
		//add_action('wp_enqueue_scripts', array( $this, 'addScripts' ));
		add_action('init', [$this, 'restFeaturedImage'], 12);
		add_action('restrict_manage_posts', [$this, 'templateDropdown']);
		add_action('pre_get_posts', [$this, 'adminListFilterByTemplate']);
	}

	public function changeTheTitle($title)
	{

		if (is_post_type_archive($this->post_type)) {
			return _x('Photographic archive', 'Archive title', 'picard');
		}

		if (is_tax('album')) {
			return '<span class="c-archive__titleprefix">' . _x('Photo album', 'Archive list header', 'sht') . '</span> ' . single_term_title('', false);
		}

		return $title;
	}

	public function postsPerAlbumPage($query)
	{
		if (!is_admin() && $query->is_main_query() && is_tax('album')) {
			$query->set('posts_per_page', 64);
			return;
		}
	}

	/**
	 * Registers the custom post type
	 * @return void
	 */
	public function registerPostType()
	{
		register_post_type(
			$this->post_type,
			[
				'can_export' => false,
				'capabilities'	=> [
					'read' => 'read_photo',
					'edit_post' => 'edit_photo',
					'read_post' => 'read_photo',
					'delete_post' => 'delete_photo',
					'edit_posts' => 'edit_photos',
					'edit_others_posts' => 'edit_others_photos',
					'publish_posts' => 'publish_photos',
					'read_private_posts' => 'read_private_photos',
					'delete_posts' => 'delete_photos',
					'delete_private_posts' => 'delete_private_photos',
					'delete_published_posts' => 'delete_published_photos',
					'delete_others_posts' => 'delete_others_photos',
					'edit_private_posts' => 'edit_private_photos',
					'edit_published_posts' => 'edit_published_photos',
				],
				'has_archive' => true,
				'map_meta_cap' => false,
				'menu_icon' => 'dashicons-welcome-widgets-menus',
				'public' => true,
				'show_in_admin_bar' => true,
				'show_in_nav_menus' => true,
				'show_in_rest' => true,
				'show_ui' => true,
				'menu_position' => 10,
				'hierarchical' => false,
				'rewrite' => [
					'slug' => 'photos'
				],
				'supports' => [
					'title',
					'editor',
					'thumbnail',
					'page-attributes'
				],
				'labels' => [
					'name' => _x('Photo', 'CPT name', 'picard'),
					'singular_name' => _x('Photo', 'CPT singular name', 'picard'),
					'add_new' => _x('Add new', 'CPT add_new', 'picard'),
					'add_new_item' => _x('Add new', 'cpt name', 'picard'),
					'edit_item' => _x('Edit photo', 'cpt name', 'picard'),
					'new_item' => _x('New photo', 'cpt name', 'picard'),
					'view_item' => _x('View photo', 'cpt name', 'picard'),
					'view_items' => _x('View photos', 'cpt name', 'picard'),
					'search_items' => _x('Search photos', 'cpt name', 'picard'),
					'not_found' => _x('No photos', 'cpt name', 'picard'),
					'not_found_in_trash' => _x('No photos in the trash', 'cpt name', 'picard'),
					'all_items' => _x('All photos', 'cpt name', 'picard'),
					'archives' => _x('Archives', 'cpt name', 'picard'),
					'attributes' => _x('Attribute', 'cpt name', 'picard'),
					'name_admin_bar' => _x('Photo', 'Label for name admin bar', 'picard'),
				]
			]
		);
	}

	/**
	 * Add user capabilities
	 */
	public function addCapabilities()
	{
		$admin = get_role('administrator');

		$admin->add_cap('read_photo');
		$admin->add_cap('edit_photo');
		$admin->add_cap('read_photo');
		$admin->add_cap('delete_photo');
		$admin->add_cap('edit_photos');
		$admin->add_cap('edit_others_photos');
		$admin->add_cap('publish_photos');
		$admin->add_cap('read_private_photos');
		$admin->add_cap('delete_photos');
		$admin->add_cap('delete_private_photos');
		$admin->add_cap('delete_published_photos');
		$admin->add_cap('delete_others_photos');
		$admin->add_cap('edit_private_photos');
		$admin->add_cap('edit_published_photos');
	}

	public function shortcodePostID()
	{
		return get_the_ID();
	}

	/**
	 * Converts a float value to a fraction value.
	 *
	 * @param float $n         The float value to be converted
	 * @param float $tolerance Rounding. Default six places.
	 *
	 * @return string The calculated fraction
	 */
	public function float2rat($n, $tolerance = 1.e-6)
	{
		$h1 = 1;
		$h2 = 0;
		$k1 = 0;
		$k2 = 1;
		$b  = 1 / $n;
		do {
			$b   = 1 / $b;
			$a   = floor($b);
			$aux = $h1;
			$h1  = $a * $h1 + $h2;
			$h2  = $aux;
			$aux = $k1;
			$k1  = $a * $k1 + $k2;
			$k2  = $aux;
			$b   = $b - $a;
		} while (abs($n - $h1 / $k1) > $n * $tolerance);

		return "$h1/$k1";
	}

	//////////////////////////////////////////////////

	public function registerCustomTaxonomies()
	{
		register_taxonomy(
			'collection',
			$this->post_type,
			[
				'labels'            => [
					'name'              => _x('Collections', 'taxonomy general name'),
					'singular_name'     => _x('Collection', 'taxonomy singular name'),
					'search_items'      => __('Search Collections'),
					'all_items'         => __('All Collections'),
					'parent_item'       => __('Parent Collection'),
					'parent_item_colon' => __('Parent Collection:'),
					'edit_item'         => __('Edit Collection'),
					'update_item'       => __('Update Collection'),
					'add_new_item'      => __('Add New Collection'),
					'new_item_name'     => __('New Collection Name'),
					'menu_name'         => __('Collection'),
				],
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => ['slug' => 'collection'],
			]
		);

		register_taxonomy(
			'place',
			$this->post_type,
			[
				'labels'            => [
					'name'              => _x('Places', 'taxonomy general name'),
					'singular_name'     => _x('Place', 'taxonomy singular name'),
					'search_items'      => __('Search Places'),
					'all_items'         => __('All Places'),
					'parent_item'       => __('Parent Place'),
					'parent_item_colon' => __('Parent Place:'),
					'edit_item'         => __('Edit Place'),
					'update_item'       => __('Update Place'),
					'add_new_item'      => __('Add New Place'),
					'new_item_name'     => __('New Place Name'),
					'menu_name'         => __('Place'),
				],
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => ['slug' => 'place'],
			]
		);

		register_taxonomy(
			'album',
			$this->post_type,
			[
				'labels'            => [
					'name'              => _x('Albums', 'taxonomy general name'),
					'singular_name'     => _x('Album', 'taxonomy singular name'),
					'search_items'      => __('Search Albums'),
					'all_items'         => __('All Albums'),
					'parent_item'       => __('Parent Album'),
					'parent_item_colon' => __('Parent Album:'),
					'edit_item'         => __('Edit Album'),
					'update_item'       => __('Update Album'),
					'add_new_item'      => __('Add New Album'),
					'new_item_name'     => __('New Album Name'),
					'menu_name'         => __('Album'),
				],
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => ['slug' => 'albums'],
			]
		);
	}

	public function postsPerPage($query)
	{
		if (!is_admin() && (is_post_type_archive($this->post_type) || is_tax('album') || is_tax('collection') || is_tax('place'))) {
			$query->set('posts_per_page', '40');
		}
	}

	public function postThumbnailMeta($content_meta_array, $post)
	{
		$thumbnailID = get_post_thumbnail_id($post->ID);
		$meta        = wp_get_attachment_metadata($thumbnailID);

		unset($content_meta_array['publishdate']);

		if ($meta && is_array($meta['image_meta'])) {
			// Date taken
			if (isset($meta['image_meta']['created_timestamp']) && intval($meta['image_meta']['created_timestamp']) !== 0) {
				$content_meta_array['created'] = [
					'type'    => 'date-taken',
					'content' => sprintf(
						'Photographed on %1$s',
						date('j\<\s\u\p\>S\<\/\s\u\p\> F Y', $meta['image_meta']['created_timestamp'])
					),
				];
			}

			// Exposure
			if (
				isset($meta['image_meta']['shutter_speed']) &&
				floatval($meta['image_meta']['shutter_speed']) > 0 &&
				isset($meta['image_meta']['aperture']) &&
				floatval($meta['image_meta']['aperture']) > 0 &&
				isset($meta['image_meta']['iso']) &&
				floatval($meta['image_meta']['iso']) > 0
			) {
				if ($meta['image_meta']['shutter_speed'] > 1) {
					$shutter_speed = $meta['image_meta']['shutter_speed'];
				} else {
					$shutter_speed = $this->float2rat(floatval($meta['image_meta']['shutter_speed']));
				}

				$content_meta_array['exposure'] = [
					'type'    => 'exif',
					'content' => sprintf(
						'Exposure: %1$ss @ %2$s, ISO %3$s',
						$shutter_speed,
						'f' . $meta['image_meta']['aperture'],
						$meta['image_meta']['iso']
					),
				];
			}

			// Camera
			if (isset($meta['image_meta']['camera']) && !empty($meta['image_meta']['camera'])) {
				$content_meta_array['camera'] = [
					'type'    => 'equipment',
					'content' => sprintf(
						'Camera: %1$s',
						$meta['image_meta']['camera']
					),
				];
			}

			// Credit and copyright
			if (isset($meta['image_meta']['copyright']) && !empty($meta['image_meta']['copyright'])) {
				$content_meta_array['credit'] = [
					'type'    => 'equipment',
					'content' => sprintf(
						'Copyright %1$s',
						$meta['image_meta']['copyright']
					),
				];
			}
		}

		return $content_meta_array;
	}

	/**
	 * Change the post slug (post_name) to the ID of the Post for this post type.
	 */
	public function changePostSlug($post_id)
	{
		$post = get_post($post_id);
		if ($post->post_type == $this->post_type) {
			wp_update_post(
				[
					'ID'        => $post_id,
					'post_name' => (string) $post_id,
				]
			);
		}
	}

	/**
	 * Add JavaScripts to the page.
	 */
	public function addScripts()
	{
		wp_enqueue_script('permanenttourist-photo-collection-api', plugins_url('Resources/Public/JavaScript/collection-api.js', __FILE__), ['jquery'], 1.5, true);
	}

	public function shortcode($atts)
	{
		$atts = shortcode_atts([
			'per_page' => '30',
			'html_before' => '<div class="frp_dataroom root">',
			'html_after' => '</div>',
			'collection' => '',
		], $atts);

		if (empty($atts['collection'])) {
			return '';
		} else {
			return '<div class="mod grid500" data-pt-photo-collection="' . $atts['collection'] . '" data-pt-photo-perpage="' . $atts['per_page'] . '"></div>';
		}
	}

	public function restFeaturedImage()
	{
		$post_types = get_post_types(['public' => true], 'objects');

		foreach ($post_types as $post_type) {
			$post_type_name = $post_type->name;
			$show_in_rest = (isset($post_type->show_in_rest) && $post_type->show_in_rest) ? true : false;
			$supports_thumbnail = post_type_supports($post_type_name, 'thumbnail');

			// Only proceed if the post type is set to be accessible over the REST API
			// and supports featured images.
			if ($show_in_rest && $supports_thumbnail) {
				// Compatibility with the REST API v2 beta 9+
				if (function_exists('register_rest_field')) {
					register_rest_field(
						$post_type_name,
						'featured_image',
						[
							'get_callback' => [$this, 'getFeaturedImages'],
							'schema' => null,
						]
					);
				} elseif (function_exists('register_api_field')) {
					register_api_field(
						$post_type_name,
						'featured_image',
						[
							'get_callback' => [$this, 'getFeaturedImages'],
							'schema' => null,
						]
					);
				}
			}
		}
	}

	public function getFeaturedImages($object, $field_name, $request)
	{

		// Only proceed if the post has a featured image.
		if (!empty($object['featured_media'])) {
			$image_id = (int) $object['featured_media'];
		} elseif (!empty($object['featured_image'])) {
			// This was added for backwards compatibility with < WP REST API v2 Beta 11.
			$image_id = (int) $object['featured_image'];
		} else {
			return;
		}

		$image = get_post($image_id);

		if (!$image) {
			return;
		}

		// This is taken from WP_REST_Attachments_Controller::prepare_item_for_response().
		$featured_image['id'] = $image_id;
		$featured_image['alt_text'] = get_post_meta($image_id, '_wp_attachment_image_alt', true);
		$featured_image['caption'] = $image->post_excerpt;
		$featured_image['description'] = $image->post_content;
		$featured_image['media_type'] = wp_attachment_is_image($image_id) ? 'image' : 'file';
		$featured_image['media_details'] = wp_get_attachment_metadata($image_id);

		$registered_sizes = get_intermediate_image_sizes();

		foreach ($registered_sizes as $registered_size) {
			$image_src = wp_get_attachment_image_src($image_id, $registered_size);

			$featured_image['media_details']['sizes'][$registered_size] = [
				'width' => $image_src[1],
				'height' => $image_src[2],
				'source_url' => $image_src[0],
			];
		}


		unset($featured_image['media_details']['file']);
		$featured_image['post'] = !empty($image->post_parent) ? (int) $image->post_parent : null;
		//$featured_image['source_url'] = wp_get_attachment_url($image_id);

		if (empty($featured_image['media_details'])) {
			$featured_image['media_details'] = new stdClass();
		} elseif (!empty($featured_image['media_details']['sizes'])) {
			//$img_url_basename = wp_basename($featured_image['source_url']);
			foreach ($featured_image['media_details']['sizes'] as $size => &$size_data) {
				$image_src = wp_get_attachment_image_src($image_id, $size);
				if (!$image_src) {
					continue;
				}
				$size_data['source_url'] = $image_src[0];
			}
		} elseif (is_string($featured_image['media_details'])) {
			// This was added to work around conflicts with plugins that cause
			// wp_get_attachment_metadata() to return a string.
			$featured_image['media_details'] = new stdClass();
			$featured_image['media_details']->sizes = new stdClass();
		} else {
			$featured_image['media_details']['sizes'] = new stdClass();
		}

		return apply_filters('featured_image', $featured_image, $image_id);
	}

	public function templateDropdown($post_type)
	{
		if ($post_type === $this->post_type) {
			$templates = get_page_templates(null, $post_type);
?>
			<select name="template_filter" id="template_filter">
				<option value=""><?php _e('All templates', 'pt-must-use'); ?></option>
				<?php foreach ($templates as $name => $key) : ?>
					<option value="<?php echo $key; ?>" <?php selected($key, isset($_GET['template_filter']) ? $_GET['template_filter'] : ''); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
<?php
		}
	}

	public function adminListFilterByTemplate($query)
	{
		global $pagenow;

		if (is_admin() && $pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === $this->post_type && isset($_GET['template_filter']) && !empty($_GET['template_filter'])) {
			$template = sanitize_text_field($_GET['template_filter']);
			$query->set('meta_key', '_wp_page_template');
			$query->set('meta_value', $template);
		}
	}

	/**
	 * Handle the 404 status for the custom post type archive
	 * if no posts are found.
	 *
	 * @return void
	 */
	public function noPosts404()
	{
		if (is_post_type_archive('photo')) {
			global $wp_query;

			// Check if there are posts in the custom post type archive query
			if (!$wp_query->have_posts()) {
				// Set 404 if no posts found
				$wp_query->set_404();
				status_header(404);
			}
		}
	}

	/**
	 * Add year and month archive URLs to the rewrite rules
	 * e.g. /photos/2024/03/
	 *
	 * @param array $rules
	 * @return array
	 */
	public function archiveRewriteRules($rules)
	{
		$new_rules = [
			'photos/([0-9]{4})/([0-9]{1,2})/?$' => 'index.php?post_type=photo&year=$matches[1]&monthnum=$matches[2]',
			'photos/([0-9]{4})/?$' => 'index.php?post_type=photo&year=$matches[1]',
		];

		return $new_rules + $rules;
	}
}
