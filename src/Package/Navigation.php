<?php

namespace PT\MustUse\Package;

/**
 * Everything to do with menus and site navigation
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Navigation
{
	public function run()
	{
		add_filter('wp_nav_menu_args', [$this, 'navMenuArgs'], 1, 1);
		add_filter('nav_menu_css_class', [$this, 'menuItemClasses'], 10, 3);
		add_filter('nav_menu_link_attributes', [$this, 'menuLinkAttributes']);
	}

	public function navMenuArgs($args)
	{
		$args['fallback_cb'] = false;
		$args['menu_class'] = 'c-menu__entries c-menu__entries--' . $args['theme_location'];
		return $args;
	}

	public function menuItemClasses($classes, $item, $args)
	{
		$classes[] = 'c-menu__entry c-menu__entry--' . $args->theme_location;
		if ($item->current) {
			$classes[] = 'c-menu__entry--current';
		}
		if ($item->current_item_ancestor) {
			$classes[] = 'c-menu__entry--current_item_ancestor';
		}
		if ($item->current_item_parent) {
			$classes[] = 'c-menu__entry--current_item_parent';
		}

		if ($item->type === 'post_type_archive' && get_post_type() === $item->object || is_singular('post') && $item->object_id === get_option('page_for_posts')) {
			$classes[] = 'current-page-ancestor';
		}

		return $classes;
	}

	public function menuLinkAttributes($atts)
	{
		if (!isset($atts['class'])) {
			$atts['class'] = '';
		}
		$atts['class'] = (!empty($atts['class']) ? ' ' : '') . 'c-menu__entrylink';
		return $atts;
	}
}
