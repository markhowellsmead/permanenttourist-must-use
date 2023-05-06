import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { gallery as icon } from '@wordpress/icons';
import { useBlockProps } from '@wordpress/block-editor';
import { AlignmentToolbar, BlockControls } from '@wordpress/block-editor';

const blockName = 'sht/series-link';

registerBlockType(blockName, {
    title: _x('Series link', 'Block title', 'sha'),
    description: _x(
        'Display a link if the current post is linked to a tag which is marked as a series.',
        'Block title',
        'sha'
    ),
    icon,
    category: 'common',
    supports: {
        align: false,
        mode: false,
        html: false,
        multiple: true,
        reusable: true,
        inserter: true,
        typography: {
            align: true,
            fontSize: true,
        },
    },
    attributes: {
        textAlignment: {
            type: 'string',
        },
    },
    edit: props => {
        const { attributes, setAttributes } = props;
        const { textAlignment } = attributes;

        const alignmentClass = textAlignment !== null ? 'has-text-align-' + textAlignment : '';

        const blockProps = useBlockProps({
            className: alignmentClass,
        });

        return (
            <>
                <BlockControls>
                    <AlignmentToolbar
                        value={attributes.textAlignment}
                        onChange={newalign => setAttributes({ textAlignment: newalign })}
                    />
                </BlockControls>
                <div {...blockProps}>
                    <p
                        dangerouslySetInnerHTML={{
                            __html: 'There are no options for this block',
                        }}
                    />
                </div>
            </>
        );
    },
});
