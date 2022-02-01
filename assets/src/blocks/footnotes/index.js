import { __, _x } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { InnerBlocks } from '@wordpress/block-editor';
import { registerBlockType, getBlockDefaultClassName } from '@wordpress/blocks';

registerBlockType('mhm/footnotes', {
	title: __('Footnotes', 'sht'),
	icon: 'list-view',
	category: 'widgets',
	keywords: ['Footnotes'],
	supports: {
		group: false,
		mode: false,
		html: false,
		multiple: false,
		reusable: false,
	},
	edit: class extends Component {
		constructor(props) {
			super(...arguments);
			this.props = props;
		}

		render() {
			const { className } = this.props;

			return (
				<div className={className}>
					<InnerBlocks
						allowedBlocks={(['core/heading'], ['core/paragraph'])}
						template={[
							[
								'core/heading',
								{
									level: 2,
									content: _x('Footnotes', 'Default content', 'sht'),
								},
							],
							['core/paragraph'],
						]}
					/>
				</div>
			);
		}
	},
	save() {
		const className = getBlockDefaultClassName('mhm/footnotes');
		return (
			<div className={className}>
				<InnerBlocks.Content />
			</div>
		);
	},
});
