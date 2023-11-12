<?php

namespace PT\MustUse\Package;

use DateTimeZone;
use WP_CLI;
use WP_Error;
use WP_REST_Attachments_Controller;
use WP_REST_Posts_Controller;
use WP_REST_Request;
use WP_REST_Response;

class AttachmentFromFTPPublish
{
	public $post_type = 'photo';
	public $post_tag = 'collection';
	public $post_title_default = '[Attachment via FTP with no image title]';

	public function run()
	{
		$this->post_type = apply_filters('mhm-attachment-from-ftp-publish/post_type', $this->post_type);
		$this->post_tag = apply_filters('mhm-attachment-from-ftp-publish/post_tag', $this->post_tag);
		$this->post_title_default = apply_filters('mhm-attachment-from-ftp-publish/post_title_default', $this->post_title_default);

		add_action('mhm-attachment-from-ftp/attachment_created', [$this, 'postFromAttachment']);
		add_action('mhm-attachment-from-ftp/title_description_overwritten', [$this, 'updatePosts']);

		add_action('rest_api_init', [$this, 'registerRestRoute']);
	}

	public function registerRestRoute()
	{
		register_rest_route('mhm/v1', '/photo-from-attachment/(?P<id>\d+)', [
			'methods' => 'GET',
			'callback' => [$this, 'restPhotoFromAttachment'],
			'permission_callback' => function () {
				return true; //current_user_can('edit_photos');
			},
		]);
	}

	public function restPhotoFromAttachment(WP_REST_Request $request)
	{
		$attachment_id = $request->get_param('id');

		$args = [
			'post_type' => $this->post_type,
			'posts_per_page' => -1,
			'meta_query' => [
				[
					'key'     => '_thumbnail_id',
					'compare' => '=',
					'value' => $attachment_id
				]
			]
		];

		$posts = get_posts($args);

		if (!empty($posts)) {
			$controller = new WP_REST_Posts_Controller('photo');

			$return_posts = [];

			foreach ($posts as $post) {
				$post_data = $controller->prepare_item_for_response($post, $request);
				$return_posts[] = $controller->prepare_response_for_collection($post_data);
			}

			return new WP_Error('posts_already_exist', 'Posts already exist', $return_posts);
		}

		$post_data = $this->postFromAttachment(attachment_id: $attachment_id, rest_response: true);

		if ($post_data) {
			$controller = new WP_REST_Attachments_Controller('attachment');
			$attachment = get_post($attachment_id);
			$data = $controller->prepare_item_for_response($attachment, $request);
			return new WP_REST_Response($data, 200);
		} else {
			return rest_ensure_response([
				'error' => 'no_post_created',
				'attachment_id' => $attachment_id,
			]);
		}
	}

	private function successLog($title, $content = '')
	{
		if (is_array($content)) {
			$content = print_r($content, 1);
		}
		error_log(date('r') . chr(9) . $title . chr(9) . $content . chr(10), 3, WP_CONTENT_DIR . '/mhm_attachment_from_ftp.success.log');
	}

	public function postFromAttachment($attachment_id, $rest_response = false)
	{
		$attachment_data = get_post($attachment_id);
		$attachment_meta_data = wp_get_attachment_metadata($attachment_id);

		if (is_array($attachment_meta_data['image_meta']) && isset($attachment_meta_data['image_meta']['keywords'])) {
			$post_tags = (array) $attachment_meta_data['image_meta']['keywords'];
		} else {
			$post_tags = [];
		}

		$post_data = apply_filters('mhm-attachment-from-ftp-publish/post_data', [
			'post_type' => $this->post_type,
			'post_author' => (int) $attachment_data->post_author,
			'post_title' => esc_attr($attachment_data->post_title),
			'post_content' => esc_attr($attachment_data->post_content),
			'post_tags' => $post_tags,
			'post_date' => wp_date('Y-m-d H:i:s', $attachment_meta_data['image_meta']['created_timestamp']),
			'post_date_gmt' => wp_date('Y-m-d H:i:s', $attachment_meta_data['image_meta']['created_timestamp'], new DateTimeZone('UTC')),
		]);

		$post_data = $this->createPost($post_data);

		if ((int) $post_data['ID']) {
			set_post_thumbnail($post_data['ID'], $attachment_id);

			$this->setPostTemplate($post_data, $attachment_meta_data);

			if ($post_data['post_title'] !== $this->post_title_default) {
				$this->publishPost($post_data['ID']);
			}
		}

		if (is_plugin_active('cachify/cachify.php')) {
			WP_CLI::runcommand('cachify flush');
			$this->successLog('Cachify flushed');
		}

		if ($rest_response) {
			return $post_data;
		}
	}

	public function createPost($data = [])
	{
		$data = array_merge([
			'post_type' => $this->post_type,
			'post_author' => get_current_user_id(),
			'post_name' => '',
			'post_title' => '',
			'post_tags' => [],
			'post_status' => 'draft',
			'post_meta' => [],
			'tax_input' => ['post_format' => 'post-format-image']
		], $data);

		/*
		 * We cannot create a regular non-Attachment-type post without a post title.
		 * If the Attachment data doesn't contain one, then assign a default title
		 * which the editor can then modify in WordPress
		 */
		if (empty($data['post_title'])) {
			$data['post_title'] = $this->post_title_default;
		}

		$this->sanitizeData($data);

		if (!$data['post_author']) {
			error_log(__METHOD__ . ': no valid author');

			return;
		}

		// Make sure a new post is always created: remove any ID passed in.
		// (This function is creating posts, not updating them.)
		if (isset($data['ID'])) {
			unset($data['ID']);
		}

		// Remove post meta into its own variable, so that wp_insert_post
		// doesn't try to save it directly to the post entry. We'll save it separately in a minute.
		$post_meta = $data['post_meta'];
		unset($data['post_meta']);

		// Store the new post in the database. If all is well, we'll get the new $post_id back.
		$data['ID'] = wp_insert_post($data);

		if (is_wp_error($data['ID'])) {
			error_log(__METHOD__ . ': unable to insert a new post');
			do_action('mhm-attachment-from-ftp-publish/post_not_created', $data['ID'], $data);
			return;
		}

		do_action('mhm-attachment-from-ftp-publish/post_created', $data['ID'], $data);

		// $this->updatePostMeta($post_id, $post_meta);
		$this->updatePostTags($data['ID'], $data, $this->post_tag);

		return $data;
	}

	/**
	 * Set post template according to image ratio
	 *
	 * Since 12.11.2023
	 *
	 * @param array $post_data
	 * @param array $attachment_data
	 * @return void
	 */
	private function setPostTemplate($post_data, $attachment_data): void
	{

		$image_ratio = $attachment_data['width'] / $attachment_data['height'];

		if ($image_ratio < 1) {
			update_post_meta($post_data['ID'], '_wp_page_template', 'single-photo-tall');
		} elseif ($image_ratio === 1) {
			update_post_meta($post_data['ID'], '_wp_page_template', 'single-photo-square');
		} elseif ($image_ratio > 2) {
			update_post_meta($post_data['ID'], '_wp_page_template', 'single-photo-panorama');
		} elseif ($image_ratio > 1.7) {
			update_post_meta($post_data['ID'], '_wp_page_template', 'single-photo-wide');
		}
	}

	public function publishPost($post_id)
	{
		if ((int) $post_id) {
			do_action('mhm-attachment-from-ftp-publish/before_post_publish', $post_id);
			wp_update_post([
				'ID' => $post_id,
				'post_status' => 'publish',
			]);
			do_action('mhm-attachment-from-ftp-publish/after_post_publish', $post_id);
		}
	}

	public function updatePostTags($post_id, $data, $post_tag_name)
	{
		if (!empty($data['post_tags'])) {
			// Retain current tags and add new ones
			wp_set_post_terms($post_id, $data['post_tags'], $post_tag_name, true);
			do_action('mhm-attachment-from-ftp-publish/post_tags_after', $post_id, $data['collection'], $post_tag_name);
		}
	}

	public function arrayMapRecursive($func, $arr)
	{
		$newArr = [];

		foreach ($arr as $key => $value) {
			$newArr[$key] = (is_array($value) ? $this->arrayMapRecursive($func, $value) : (is_array($func) ? call_user_func_array($func, $value) : $func($value)));
		}

		return $newArr;
	}

	public function sanitizeData(&$data)
	{
		$data = $this->arrayMapRecursive('strip_tags', $data);
	}

	public function updatePosts($attachment_id)
	{

		$attachment_meta_data = wp_get_attachment_metadata($attachment_id);

		if (is_array($attachment_meta_data['image_meta']) && isset($attachment_meta_data['image_meta']['keywords'])) {
			if (!empty($attachment_meta_data['image_meta']['keywords'] ?? [])) {
				if (empty($posts = get_posts([
					'post_type' => $this->post_type,
					'posts_per_page' => -1,
					'meta_query' => [
						[
							'key'     => '_thumbnail_id',
							'compare' => '=',
							'value' => $attachment_id
						]
					]
				]))) {
					return;
				}

				foreach ($posts as $post) {
					// Remove and replace ALL tags
					wp_set_post_terms($post->ID, $attachment_meta_data['image_meta']['keywords'], $this->post_tag, false);
				}
			}
		}
	}
}
