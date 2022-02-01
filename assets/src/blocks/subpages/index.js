import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('mhm/subpages', {
    title: _x('Subpages', 'Block title', 'sha'),
    icon: 'post',
    category: 'widgets',
    supports: {
        align: ['wide', 'full'],
    },
    edit: class extends Component {
        render() {
            return <ServerSideRender block='mhm/subpages' />;
        }
    },
    save() {
        return null;
    },
});
