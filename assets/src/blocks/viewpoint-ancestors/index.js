import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('mhm/viewpoint-ancestors', {
    title: _x('Viewpoint ancestors', 'Block title', 'sha'),
    icon: 'layout',
    category: 'widgets',
    supports: {
        align: ['wide', 'full'],
        mode: false,
        html: false,
        multiple: true,
        reusable: true,
    },
    edit: class extends Component {
        render() {
            return <ServerSideRender block='mhm/viewpoint-ancestors' />;
        }
    },
    save() {
        return null;
    },
});
