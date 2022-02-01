import { __, _x } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { InnerBlocks } from '@wordpress/block-editor';
import { registerBlockType, getBlockDefaultClassName } from '@wordpress/blocks';

registerBlockType('mhm/project-grid', {
	title: __('Project grid', 'sht'),
	icon: 'layout',
	category: 'widgets',
	keywords: ['grid'],
	supports: {
		align: ['wide', 'full'],
		group: false,
		mode: false,
		html: false,
		multiple: true,
		reusable: true,
	},
	attributes: {
		align: {
			type: 'string',
			default: 'full',
		},
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
						allowedBlocks={['core/columns', 'mhm/project-header']}
						template={[
							['mhm/project-header'],
							[
								'core/columns',
								{
									align: 'full',
									className: 'is-style-gapless',
								},
							],
						]}
						templateLock={false}
					/>
				</div>
			);
		}
	},
	save() {
		const className = getBlockDefaultClassName('mhm/project-grid');
		return (
			<div className={className}>
				<InnerBlocks.Content />
			</div>
		);
	},
});
