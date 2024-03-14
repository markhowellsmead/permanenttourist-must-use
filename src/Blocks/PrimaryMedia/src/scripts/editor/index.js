import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow, SelectControl, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { _x } from '@wordpress/i18n';
import block_json from '../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
    edit: props => {
        const blockProps = useBlockProps();
        const { attributes, setAttributes } = props;
        const { hideInlineEmbed, resolution } = attributes;

        console.log(attributes);

        const toggleHideInlineEmbed = () => {
            setAttributes({ hideInlineEmbed: !hideInlineEmbed });
        };

        const availableImageSizes = useSelect(select => {
            return select('core/editor').getEditorSettings().imageSizes;
        }, []);

        return (
            <>
                <InspectorControls>
                    <PanelBody title={_x('Settings', 'Block settings', 'textdomain')}>
                        <PanelRow>
                            <ToggleControl
                                label={_x('Hide inline embed', 'Block setting', 'textdomain')}
                                checked={hideInlineEmbed}
                                onChange={toggleHideInlineEmbed}
                            />
                        </PanelRow>
                        <PanelRow>
                            <SelectControl
                                label={_x('Image resolution', 'Block setting', 'textdomain')}
                                value={resolution}
                                options={availableImageSizes.map(size => ({
                                    label: size.name,
                                    value: size.slug,
                                }))}
                                onChange={resolution => setAttributes({ resolution })}
                            />
                        </PanelRow>
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    <p>This block currently has no preview.</p>
                </div>
            </>
        );
    },
});
