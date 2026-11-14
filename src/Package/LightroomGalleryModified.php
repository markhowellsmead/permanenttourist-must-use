<?php

namespace PT\MustUse\Package;

/**
 * Updates the modified date of every post (any post type) which contains
 * a Meow Gallery showing a WP/LR Sync collection, whenever that collection
 * changes via Lightroom.
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class LightroomGalleryModified
{

	/**
	 * WP/LR collection IDs changed during the current request.
	 *
	 * @var array
	 */
	private array $collection_ids = [];

	public function run()
	{
		add_action('wplr_add_media_to_collection', [$this, 'rememberCollection'], 10, 2);
		add_action('wplr_remove_media_from_collection', [$this, 'rememberCollection'], 10, 2);
		add_action('wplr_order_collection', [$this, 'rememberCollection'], 10, 2);
		add_action('wplr_update_media', [$this, 'rememberCollectionsFromMedia'], 10, 2);
		add_action('shutdown', [$this, 'touchPosts']);
	}

	/**
	 * Hooked to actions which pass the collection ID as the second argument.
	 *
	 * @param mixed $media
	 * @param int $collection_id
	 * @return void
	 */
	public function rememberCollection($media, $collection_id): void
	{
		$this->collection_ids[(int) $collection_id] = true;
	}

	/**
	 * A photo which was re-published from Lightroom changes every collection it is in.
	 *
	 * @param int $media_id
	 * @param array $collection_ids
	 * @return void
	 */
	public function rememberCollectionsFromMedia($media_id, $collection_ids): void
	{
		foreach ((array) $collection_ids as $collection_id) {
			$this->collection_ids[(int) $collection_id] = true;
		}
	}

	/**
	 * Run once at the end of the request, so that a sync of many photos
	 * only updates each post once.
	 *
	 * The database is updated directly rather than via wp_update_post, so that
	 * save_post and its side effects (revisions, social sharing etc.) don't run.
	 *
	 * @return void
	 */
	public function touchPosts(): void
	{
		unset($this->collection_ids[0]);

		if (empty($this->collection_ids)) {
			return;
		}

		global $wpdb;

		$where = [];

		foreach (array_keys($this->collection_ids) as $collection_id) {
			// Shortcode attribute in the block's saved content, and the block comment attribute
			$where[] = $wpdb->prepare('post_content LIKE %s', '%' . $wpdb->esc_like('wplr-collection="' . $collection_id . '"') . '%');
			$where[] = $wpdb->prepare('post_content LIKE %s', '%' . $wpdb->esc_like('"wplrCollection":"' . $collection_id . '"') . '%');
		}

		$post_ids = $wpdb->get_col(
			"SELECT ID FROM {$wpdb->posts}
			WHERE post_type NOT IN ('revision', 'attachment')
			AND post_status NOT IN ('trash', 'auto-draft', 'inherit')
			AND (" . implode(' OR ', $where) . ")"
		);

		if (empty($post_ids)) {
			return;
		}

		$now = current_time('mysql');
		$now_gmt = current_time('mysql', true);

		foreach ($post_ids as $post_id) {
			$wpdb->update(
				$wpdb->posts,
				[
					'post_modified' => $now,
					'post_modified_gmt' => $now_gmt,
				],
				['ID' => (int) $post_id]
			);

			clean_post_cache((int) $post_id);

			// LiteSpeed Cache API
			do_action('litespeed_purge_post', (int) $post_id);
		}

		$this->collection_ids = [];
	}
}
