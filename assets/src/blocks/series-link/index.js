import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { gallery as icon } from '@wordpress/icons';
import { useBlockProps } from '@wordpress/block-editor';

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
        align: ['center', 'wide', 'full'],
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
