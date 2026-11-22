import { _x, sprintf } from '@wordpress/i18n';
import { TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { registerPlugin } from '@wordpress/plugins';
import { registerBlockBindingsSource } from '@wordpress/blocks';

const validPostTypes = ['mhm_feature'];

const FeatureMetaPanel = () => {
    const postType = useSelect(select => select('core/editor').getCurrentPostType(), []);

    const meta = useSelect(select => select('core/editor').getEditedPostAttribute('meta'), []);

    const { editPost } = useDispatch('core/editor');

    if (!postType || !validPostTypes.includes(postType)) {
        return null;
    }

    return (
        <PluginDocumentSettingPanel
            name='pt-must-use-feature-data'
            title={_x('Feature data', 'Editor sidebar panel title', 'pt-must-use')}
            initialOpen={true}
        >
            <TextControl
                label={_x('Featured image description', 'Field label', 'pt-must-use')}
                help={_x(
                    'Shown with the featured image in the feature template.',
                    'Field help',
                    'pt-must-use'
                )}
                value={meta?.featured_image_description || ''}
                onChange={value => editPost({ meta: { featured_image_description: value } })}
            />
        </PluginDocumentSettingPanel>
    );
};

registerPlugin('pt-must-use-mhm-feature-controls', { render: FeatureMetaPanel });

/**
 * Editor counterpart of the pt/featured-image-description block binding source
 * (registered in PHP). Shows the same prefixed value in the editor.
 */
registerBlockBindingsSource({
    name: 'pt/featured-image-description',
    label: _x('Featured image description', 'Block binding source label', 'pt-must-use'),
    usesContext: ['postId', 'postType'],
    getValues({ select, context }) {
        const record = select('core').getEditedEntityRecord(
            'postType',
            context.postType,
            context.postId
        );
        const value = (record?.meta?.featured_image_description || '').trim();

        return {
            content: value
                ? sprintf(
                      /* translators: %s: description of the featured image */
                      _x('Lead photo: %s', 'Featured image description prefix', 'pt-must-use'),
                      value
                  )
                : '',
        };
    },
    getFieldsList() {
        return [
            {
                label: _x('Featured image description', 'Field label', 'pt-must-use'),
                type: 'string',
                args: {},
            },
        ];
    },
});
