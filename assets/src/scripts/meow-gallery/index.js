import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';
import { _x, __ } from '@wordpress/i18n';
import lodash from 'lodash';

const allowedBlocks = ['meow-gallery/gallery'];

/**
 * Add custom attribute to the block.
 */
addFilter('blocks.registerBlockType', 'sht/meow-gallery/gallery', settings => {
    if (!allowedBlocks.includes(settings.name)) {
        return settings;
    }

    return lodash.assign({}, settings, {
        attributes: lodash.assign({}, settings.attributes, {
            shpCustomSort: {
                type: 'string',
                default: 'added-asc',
            },
            blockId: {
                type: 'string',
                default: '',
            },
        }),
    });
});

/**
 * Add custom inspector controls to the block.
 */
addFilter('editor.BlockEdit', 'sht/meow-gallery/gallery', BlockEdit => {
    return props => {
        const { blockId, name, attributes, setAttributes, isSelected, clientId } = props;

        const { shpCustomSort } = attributes;

        if (!isSelected || !allowedBlocks.includes(name)) {
            return <BlockEdit {...props} />;
        }

        if (!blockId && !!clientId) {
            setAttributes({ blockId: clientId });
        }

        return <BlockEdit {...props} />;

        // return (
        //     <>
        //         <BlockEdit {...props} />
        //         <InspectorControls>
        //             <PanelBody title={__('Custom Sort', 'pt-must-use')} initialOpen={true}>
        //                 <SelectControl
        //                     label={__('Sort Order', 'pt-must-use')}
        //                     value={shpCustomSort}
        //                     options={[
        //                         {
        //                             label: __('Added date (newest first)', 'pt-must-use'),
        //                             value: 'added-asc',
        //                         },
        //                         {
        //                             label: __('Added date (oldest first)', 'pt-must-use'),
        //                             value: 'added-desc',
        //                         },
        //                         {
        //                             label: __('Modified date (oldest first)', 'pt-must-use'),
        //                             value: 'modified-asc',
        //                         },
        //                         {
        //                             label: __('Modified date (newest first)', 'pt-must-use'),
        //                             value: 'modified-desc',
        //                         },
        //                     ]}
        //                     onChange={value =>
        //                         setAttributes({
        //                             shpCustomSort: value,
        //                         })
        //                     }
        //                 />
        //             </PanelBody>
        //         </InspectorControls>
        //     </>
        // );
    };
});
