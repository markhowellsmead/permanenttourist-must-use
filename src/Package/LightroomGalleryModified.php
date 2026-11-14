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

		$posts = $wpdb->get_results(
			"SELECT ID, post_type FROM {$wpdb->posts}
			WHERE post_type NOT IN ('revision', 'attachment')
			AND post_status NOT IN ('trash', 'auto-draft', 'inherit')
			AND (" . implode(' OR ', $where) . ")"
		);

		if (empty($posts)) {
			return;
		}

		$post_ids = array_map('intval', wp_list_pluck($posts, 'ID'));
		$post_types = array_unique(wp_list_pluck($posts, 'post_type'));

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

		$this->purgeQueryLoops($post_types, $post_ids);

		$this->collection_ids = [];
	}

	/**
	 * Purges the LiteSpeed cache of every post which contains a Query Loop
	 * block listing one of the given post types. The modified date of these
	 * posts isn't changed, as their own content hasn't changed.
	 *
	 * Archive templates (Query Loops which inherit the main query) are already
	 * purged by LiteSpeed's own rules for the updated posts.
	 *
	 * @param array $post_types
	 * @param array $exclude_ids Posts which have already been purged.
	 * @return void
	 */
	private function purgeQueryLoops(array $post_types, array $exclude_ids): void
	{
		global $wpdb;

		$posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_type, post_content FROM {$wpdb->posts}
				WHERE post_status IN ('publish', 'private')
				AND post_type NOT IN ('revision', 'attachment')
				AND post_content LIKE %s",
				'%' . $wpdb->esc_like('<!-- wp:query ') . '%'
			)
		);

		$purge_ids = [];
		$pattern_ids = [];

		foreach ($posts as $post) {
			if (array_intersect($post_types, $this->getQueryLoopPostTypes(parse_blocks($post->post_content)))) {
				$purge_ids[] = (int) $post->ID;

				if ($post->post_type === 'wp_block') {
					$pattern_ids[] = (int) $post->ID;
				}
			}
		}

		// Synced patterns aren't pages, so also purge the posts which use them.
		if (!empty($pattern_ids)) {
			$where = [];

			foreach ($pattern_ids as $pattern_id) {
				$where[] = $wpdb->prepare('post_content LIKE %s', '%' . $wpdb->esc_like('<!-- wp:block {"ref":' . $pattern_id . '}') . '%');
			}

			$purge_ids = array_merge($purge_ids, array_map('intval', $wpdb->get_col(
				"SELECT ID FROM {$wpdb->posts}
				WHERE post_status IN ('publish', 'private')
				AND post_type NOT IN ('revision', 'attachment')
				AND (" . implode(' OR ', $where) . ")"
			)));
		}

		foreach (array_diff(array_unique($purge_ids), $exclude_ids) as $post_id) {
			do_action('litespeed_purge_post', $post_id);
		}
	}

	/**
	 * Recursively collects the post types listed by Query Loop blocks.
	 * Includes the additional post types from the Advanced Query Loop plugin.
	 *
	 * @param array $blocks
	 * @return array
	 */
	private function getQueryLoopPostTypes(array $blocks): array
	{
		$post_types = [];

		foreach ($blocks as $block) {
			if ($block['blockName'] === 'core/query') {
				$query = $block['attrs']['query'] ?? [];

				if (empty($query['inherit'])) {
					$post_types[] = $query['postType'] ?? 'post';
					$post_types = array_merge($post_types, (array) ($query['multiple_posts'] ?? []));
				}
			}

			if (!empty($block['innerBlocks'])) {
				$post_types = array_merge($post_types, $this->getQueryLoopPostTypes($block['innerBlocks']));
			}
		}

		return array_unique($post_types);
	}
}
