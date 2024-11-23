import { _x, __ } from '@wordpress/i18n';
import { ColorPaletteControl } from '@wordpress/block-editor';
import { ToggleControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { registerPlugin } from '@wordpress/plugins';

const validPostTypes = ['page'];

const isValidPostType = function (name) {
    return validPostTypes.includes(name);
};

let CustomLayoutPanel = () => {
    const postType = useSelect(select => select('core/editor').getCurrentPostType());

    if (!postType || !isValidPostType(postType)) {
        return null;
    }

    const themeColors = useSelect(select => {
        const settings = select('core/block-editor').getSettings();
        return settings?.colors || [];
    }, []);

    const { content_behind_masthead, masthead_color } = useSelect(select => {
        const meta = select('core/editor').getEditedPostAttribute('meta');
        return meta || {};
    });

    const { editPost } = useDispatch('core/editor');

    const handleContentBehindMasthead = value => {
        editPost({ meta: { content_behind_masthead: value } });
    };

    const handleColorChange = color => {
        editPost({ meta: { masthead_color: color } });
    };

    return (
        <PluginDocumentSettingPanel
            title={_x('Page layout options', 'Editor sidebar panel title', 'pt-must-use')}
            initialOpen={true}
        >
            <ToggleControl
                label={'Content behind masthead'}
                onChange={() => handleContentBehindMasthead(!content_behind_masthead)}
                checked={!!content_behind_masthead}
            />
            {!!content_behind_masthead && (
                <div>
                    <label>{__('Masthead Background Color', 'pt-must-use')}</label>
                    <ColorPaletteControl value={masthead_color} onChange={handleColorChange} />
                </div>
            )}
        </PluginDocumentSettingPanel>
    );
};

registerPlugin('pt-must-use-page-controls', { render: CustomLayoutPanel });
