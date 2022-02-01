import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('mhm/years-online', {
	title: _x('Years online', 'Block title', 'sha'),
	icon: 'image-flip-horizontal',
	category: 'widgets',
	supports: {
		mode: false,
		html: false,
		multiple: true,
		reusable: true,
	},
	edit: class extends Component {
		render() {
			return <ServerSideRender block='mhm/years-online' />;
		}
	},
	save() {
		return null;
	},
});
