import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { gallery as icon } from '@wordpress/icons';
import { useBlockProps } from '@wordpress/block-editor';

const blockName = 'sht/primary-media';

registerBlockType(blockName, {
	title: _x('Primary media', 'Block title', 'sha'),
	description: _x('Adds the primary media output.', 'Block title', 'sha'),
	icon,
	category: 'common',
	supports: {
		align: ['wide', 'full'],
		mode: false,
		html: false,
		multiple: true,
		reusable: true,
		inserter: true,
		typography: {
			fontSize: true,
		},
	},
	attributes: {
		align: {
			type: 'string',
		},
	},
	edit: () => {
		const blockProps = useBlockProps();

		return (
			<div {...blockProps}>
				<p
					dangerouslySetInnerHTML={{
						__html: 'There are no options for this block',
					}}
				/>
			</div>
		);
	},
});
