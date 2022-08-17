import { _x } from '@wordpress/i18n';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { ToggleControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withDispatch, withSelect } from '@wordpress/data';

// Page titles are never output directly
const validPostTypes = ['post'];

const isValidPostType = function (name) {
    return validPostTypes.includes(name);
};

const PageOptionsPanel = ({ hide_title, post_type, onUpdateHideTitle }) => {
    if (!post_type || !isValidPostType(post_type)) {
        return null;
    }

    return (
        <PluginDocumentSettingPanel
            title={_x('Page options', 'Editor sidebar panel title', 'sht')}
            initialOpen={true}
            icon={'invalid-name-no-icon'}
        >
            <ToggleControl
                label={_x('Hide title', 'ToggleControl label', 'sha')}
                help={
                    hide_title
                        ? _x(
                              'The title is hidden for the current post. It is recommended that you add a H1 to the content in order to better support search engines.',
                              'Warning text',
                              'sha'
                          )
                        : ''
                }
                checked={hide_title}
                onChange={hide_title => onUpdateHideTitle(hide_title)}
            />
        </PluginDocumentSettingPanel>
    );
};

const PageOptionsPanelWithCompose = compose([
    withSelect(select => {
        let post_type = select('core/editor').getCurrentPostType(),
            data = {
                post_type: post_type,
            };

        if (isValidPostType(post_type)) {
            data.hide_title = select('core/editor').getEditedPostAttribute('meta')['hide_title'];
        }
        return data;
    }),
    withDispatch(dispatch => {
        return {
            onUpdateHideTitle: metaValue => {
                dispatch('core/editor').editPost({ meta: { hide_title: metaValue } });
            },
        };
    }),
])(PageOptionsPanel);

registerPlugin('sht-page-controls', { render: PageOptionsPanelWithCompose });
