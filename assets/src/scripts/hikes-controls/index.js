import { _x, __ } from '@wordpress/i18n';
import { ColorPaletteControl } from '@wordpress/block-editor';
import { BaseControl, TextControl, ToggleControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { registerPlugin } from '@wordpress/plugins';

const validPostTypes = ['mhm_hike'];

const isValidPostType = function (name) {
    return validPostTypes.includes(name);
};

let CustomMetaPanel = () => {
    const postType = useSelect(select => select('core/editor').getCurrentPostType());

    if (!postType || !isValidPostType(postType)) {
        return null;
    }

    const { outdooractive_url } = useSelect(select => {
        const meta = select('core/editor').getEditedPostAttribute('meta');
        return meta || {};
    });

    const { editPost } = useDispatch('core/editor');

    const handleOutdooractiveUrlChange = value => {
        editPost({ meta: { outdooractive_url: value } });
    };

    return (
        <PluginDocumentSettingPanel
            title={_x('Hike data', 'Editor sidebar panel title', 'pt-must-use')}
            initialOpen={true}
        >
            <TextControl
                label={'Outdooractive URL'}
                onChange={handleOutdooractiveUrlChange}
                value={outdooractive_url}
            />
        </PluginDocumentSettingPanel>
    );
};

registerPlugin('pt-must-use-mhm-hike-controls', { render: CustomMetaPanel });
