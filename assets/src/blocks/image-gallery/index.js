import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('acf/image-gallery', {
    title: _x('Image gallery', 'Block title', 'sha'),
    icon: 'image-flip-horizontal',
    category: 'widgets',
    supports: {
        mode: false,
        html: false,
        multiple: false,
        reusable: false,
    },
    edit: class extends Component {
        render() {
            return <ServerSideRender block='acf/image-gallery' />;
        }
    },
    save() {
        return null;
    },
});
