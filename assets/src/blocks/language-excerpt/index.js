import { _x } from '@wordpress/i18n';
import { registerBlockType, getBlockDefaultClassName } from '@wordpress/blocks';
import { Fragment } from '@wordpress/element';
import { RichText } from '@wordpress/block-editor';

import edit from './edit';

registerBlockType('mhm/language-excerpt', {
    title: _x('Language excerpt', 'Block title', 'pt-must-use'),
    description: _x(
        'This block allows the provision of a text excerpt in an alternative language.',
        'Block instructions',
        'pt-must-use'
    ),
    icon: 'excerpt-view',
    category: 'widgets',
    supports: {
        mode: false,
        html: false,
        multiple: false,
        reusable: false,
    },
    attributes: {
        lang: {
            type: 'string',
            default: 'en',
        },
        text: {
            type: 'string',
            default: '',
        },
    },
    keywords: ['excerpt', 'language'],
    edit,
    save({ attributes }) {
        const { lang, text } = attributes;
        const className = getBlockDefaultClassName('mhm/language-excerpt');
        return (
            <Fragment>
                {text && (
                    <section className={className} lang={lang}>
                        <RichText.Content value={text} />
                    </section>
                )}
            </Fragment>
        );
    },
});
