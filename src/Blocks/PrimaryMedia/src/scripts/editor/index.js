import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow, ToggleControl } from '@wordpress/components';
import { _x } from '@wordpress/i18n';
import block_json from '../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
    edit: props => {
        const blockProps = useBlockProps();
        const { attributes, setAttributes } = props;
        const { hideInlineEmbed } = attributes;

        const toggleHideInlineEmbed = () => {
            setAttributes({ hideInlineEmbed: !hideInlineEmbed });
        };

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
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    <p>This block currently has no preview.</p>
                </div>
            </>
        );
    },
});
