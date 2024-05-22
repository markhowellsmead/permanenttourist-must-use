<?php

namespace PT\MustUse\Package;

/**
 * Admin
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Admin
{

	private $templates = [];
	private $theme_json = [];

	public function run()
	{

		global $pagenow;

		if (is_admin() && $pagenow === 'edit.php') {
			if (empty($this->theme_json)) {
				$this->theme_json = json_decode(file_get_contents(get_template_directory() . '/theme.json'), true);
			}

			if (empty($this->templates)) {
				$this->templates = $this->theme_json['customTemplates'] ?? [];
			}
		}

		add_filter('manage_posts_columns', [$this, 'addTemplateColumn']);
		add_filter('manage_pages_columns', [$this, 'addTemplateColumn']);

		add_action('manage_posts_custom_column', [$this, 'addTemplateColumnContent'], 10, 2);
		add_action('manage_pages_custom_column', [$this, 'addTemplateColumnContent'], 10, 2);

		// add dropdown to filter list by selected template
		add_action('restrict_manage_posts', [$this, 'addTemplateFilter']);
		add_filter('parse_query', [$this, 'filterTemplateQuery']);
	}

	public function addTemplateColumn($columns)
	{
		$columns['template'] = __('Template', 'sht');
		return $columns;
	}

	public function addTemplateColumnContent($column_name, $post_id)
	{
		if ($column_name === 'template') {
			$template = get_page_template_slug($post_id);
			if ($template) {
				foreach ($this->templates as $template_entry) {
					if ($template_entry['name'] === $template) {
						$template = $template_entry['title'];
						break;
					}
				}

				echo $template;
			}
		}
	}

	public function addTemplateFilter()
	{

		global $post_type;

		if (in_array($post_type, ['post', 'page'])) {
?>
			<select name="template">
				<option value=""><?php _e('All templates', 'sht'); ?></option>
				<?php
				foreach ($this->templates as $template) {
				?>
					<option value="<?php echo $template['name']; ?>" <?php selected($_GET['template'] ?? '', $template['name']); ?>><?php echo $template['title']; ?></option>
				<?php
				}
				?>
			</select>
<?php
		}
	}

	public function filterTemplateQuery($query)
	{

		global $post_type, $pagenow;

		if (in_array($post_type, ['post', 'page']) && is_admin() && $pagenow === 'edit.php' && isset($_GET['template']) && $_GET['template'] !== '') {
			$query->query_vars['meta_key'] = '_wp_page_template';
			$query->query_vars['meta_value'] = $_GET['template'];
		}
	}
}
