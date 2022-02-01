import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('mhm/albums', {
    title: _x('Albums', 'Block title', 'sha'),
    icon: 'image-flip-horizontal',
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
            return <ServerSideRender block='mhm/albums' />;
        }
    },
    save() {
        return null;
    },
});
