import { _x } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { ServerSideRender } from '@wordpress/components';
import { Component } from '@wordpress/element';

registerBlockType('mhm/publication-date', {
    title: _x('Publication date', 'Block title', 'sha'),
    icon: 'calendar-alt',
    category: 'widgets',
    supports: {
        align: ['left', 'center'],
        mode: false,
        html: false,
        multiple: false,
        reusable: false,
    },
    keywords: [],
    attributes: {
        align: {
            type: 'string',
            default: '',
        },
    },
    edit: class extends Component {
        render() {
            const { attributes } = this.props;
            const { align } = attributes;
            return (
                <ServerSideRender
                    block='mhm/publication-date'
                    attributes={{
                        align: align,
                    }}
                />
            );
        }
    },
    save() {
        return null;
    },
});
