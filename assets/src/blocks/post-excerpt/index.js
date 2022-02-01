import { _x } from '@wordpress/i18n';
import { registerBlockType, getBlockDefaultClassName } from '@wordpress/blocks';
import { Fragment } from '@wordpress/element';

import edit from './edit';

registerBlockType('mhm/post-excerpt', {
	title: _x('Post excerpt', 'Block title', 'picard'),
	description: _x('This block automatically shows the current post excerpt. There are no options for this block. Edit the post excerpt as usual.', 'Block instructions', 'picard'),
	icon: 'excerpt-view',
	category: 'widgets',
	supports: {
		mode: false,
		html: false,
		multiple: false,
		reusable: false,
	},
	attributes: {
		excerpt: {
			type: 'string',
			default: '',
		},
	},
	keywords: ['excerpt'],
	edit,
	save({ attributes }) {
		const { excerpt } = attributes;
		const className = getBlockDefaultClassName('mhm/post-excerpt');
		return <Fragment>{excerpt && <section className={className}>{excerpt}</section>}</Fragment>;
	},
});
