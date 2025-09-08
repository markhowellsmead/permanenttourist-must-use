import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { _x } from '@wordpress/i18n';
import block_json from '../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();

        return (
            <>
                <InspectorControls>
                    <PanelBody title='Outdooractive Embed Settings' initialOpen={true}>
                        <TextControl
                            label='Title'
                            value={attributes.title || ''}
                            onChange={value => setAttributes({ title: value })}
                            help='Set a title for this embed.'
                        />
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    <p>This block currently has no preview.</p>
                </div>
            </>
        );
    },
});
