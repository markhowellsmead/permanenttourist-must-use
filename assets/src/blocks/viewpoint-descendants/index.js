import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { SelectControl, PanelBody, ServerSideRender } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';

registerBlockType('mhm/viewpoint-descendants', {
    title: _x('Viewpoint descendants', 'Block title', 'sha'),
    icon: 'layout',
    category: 'widgets',
    supports: {
        align: ['wide', 'full'],
        mode: false,
        html: false,
        multiple: false,
        reusable: false,
    },
    attributes: {
        viewpoint_type: {
            type: 'string',
            default: 'place',
        },
    },
    edit({ attributes, setAttributes }) {
        const { viewpoint_type } = attributes;
        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title='Block options' initialOpen={true}>
                        <SelectControl
                            label='Select viewpoint type'
                            value={viewpoint_type}
                            options={[
                                { label: 'Country', value: 'country' },
                                { label: 'Region', value: 'region' },
                                { label: 'Place', value: 'place', isDefault: true },
                            ]}
                            onChange={value => {
                                setAttributes({ viewpoint_type: value });
                            }}
                        />
                    </PanelBody>
                </InspectorControls>
                <ServerSideRender block='mhm/viewpoint-descendants' attributes={attributes} />
            </Fragment>
        );
    },
    save() {
        return null;
    },
});
