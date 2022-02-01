import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('mhm/count-photos', {
	title: _x('Number of photos', 'Block title', 'sha'),
	icon: 'image-flip-horizontal',
	category: 'widgets',
	supports: {
		mode: false,
		html: false,
		multiple: true,
		reusable: true,
	},
	keywords: ['count', 'photos'],
	edit: class extends Component {
		render() {
			return <ServerSideRender block='mhm/count-photos' />;
		}
	},
	save() {
		return null;
	},
});
