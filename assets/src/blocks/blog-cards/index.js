import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('mhm/blog-cards', {
	title: _x('Blog posts as cards', 'Block title', 'sha'),
	icon: 'image-flip-horizontal',
	category: 'widgets',
	supports: {
		align: ['wide', 'full'],
		mode: false,
		html: false,
		multiple: true,
		reusable: true,
	},
	keywords: ['cards', 'posts', 'blog'],
	edit: class extends Component {
		render() {
			return <ServerSideRender block='mhm/blog-cards' />;
		}
	},
	save() {
		return null;
	},
});
