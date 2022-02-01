import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('mhm/viewpoint-cards', {
	title: _x('Viewpoints as cards', 'Block title', 'sha'),
	icon: 'image-flip-horizontal',
	category: 'widgets',
	supports: {
		align: ['wide', 'full'],
		mode: false,
		html: false,
		multiple: true,
		reusable: true,
	},
	keywords: ['cards', 'posts', 'viewpoint'],
	edit: class extends Component {
		render() {
			return <ServerSideRender block='mhm/viewpoint-cards' />;
		}
	},
	save() {
		return null;
	},
});
