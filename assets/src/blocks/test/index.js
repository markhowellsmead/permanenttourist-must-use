import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';

const blockName = 'sht/test';

registerBlockType(blockName, {
    apiVersion: 2,
    title: __('Test Block', 'sht'),
    description: __('Test Block description', 'sht'),
    icon: 'lock',
    category: 'theme',
    edit: () => {
        const blockProps = useBlockProps();
        return <div {...blockProps}>Test block (editor)</div>;
    },
    save: () => {
        const blockProps = useBlockProps.save();
        return <div {...blockProps}>Test block (website)</div>;
    },
});
