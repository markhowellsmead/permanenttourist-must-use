import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow, SelectControl, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { _x } from '@wordpress/i18n';

import { FeaturedImage } from './featured-image';

import block_json from '../../../block.json';
const { name: block_name } = block_json;

const classNameBase = getBlockDefaultClassName(block_name);

registerBlockType(block_name, {
    edit: props => {
        const blockProps = useBlockProps();
        const { attributes, setAttributes, context } = props;
        const { hideInlineEmbed, resolution, className } = attributes;
        const { postId, postType } = context;

        if (!context || !postId || !postType) {
            return (
                <div {...blockProps}>
                    <div
                        className={`${className} ${classNameBase}__figure ${classNameBase}__figure--empty`}
                    >
                        <p
                            dangerouslySetInnerHTML={{
                                __html: _x(
                                    'The preview of this block only appears in a page or post context .',
                                    'Block placeholder',
                                    'pt-must-use'
                                ),
                            }}
                        />
                    </div>
                </div>
            );
        }

        const postData = useSelect(select => {
            return select('core').getEntityRecord('postType', postType, postId);
        });

        const toggleHideInlineEmbed = () => {
            setAttributes({ hideInlineEmbed: !hideInlineEmbed });
        };

        const availableImageSizes = useSelect(select => {
            return select('core/editor').getEditorSettings().imageSizes;
        }, []);

        return (
            <>
                <InspectorControls>
                    <PanelBody title={_x('Settings', 'Block settings', 'pt-must-use')}>
                        <PanelRow>
                            <ToggleControl
                                label={_x('Hide inline embed', 'Block setting', 'pt-must-use')}
                                checked={hideInlineEmbed}
                                onChange={toggleHideInlineEmbed}
                            />
                        </PanelRow>
                        <PanelRow>
                            <SelectControl
                                label={_x('Image resolution', 'Block setting', 'pt-must-use')}
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
                    <FeaturedImage
                        postData={postData}
                        resolution={resolution}
                        className={className}
                        classNameBase={classNameBase}
                    />
                </div>
            </>
        );
    },
});
