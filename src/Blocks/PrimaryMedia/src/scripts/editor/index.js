import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { _x } from '@wordpress/i18n';
import block_json from '../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
    edit: () => {
        const blockProps = useBlockProps();

        return (
            <div {...blockProps}>
                <p>This block currently has no preview.</p>
            </div>
        );
    },
});
