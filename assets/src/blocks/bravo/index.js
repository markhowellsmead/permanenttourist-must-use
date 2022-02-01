import { RichText } from '@wordpress/block-editor';
import { Component } from '@wordpress/element';
import { __, _x } from '@wordpress/i18n';
import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';

import LazyImageSelector from '../_components/LazyImageSelector';
import { LazyImage } from '../_components/LazyImage';
import { BlockTitle } from '../_components/blocktitles';

registerBlockType('mhm/bravo', {
    title: _x('Bravo', 'Block title', 'sha'),
    icon: 'layout',
    category: 'widgets',
    keywords: [_x('Bravo', 'Gutenberg block keyword', 'sha')],
    supports: {
        align: ['wide', 'full'],
        html: false,
        inserter: true,
    },
    styles: [
        { name: 'default', label: _x('Default', 'block style', 'sha'), isDefault: true },
        { name: 'flipped', label: _x('Flipped', 'block style', 'sha') },
    ],
    attributes: {
        title: {
            type: 'string',
            default: '',
        },
        text: {
            type: 'string',
            default: '',
        },
        image: {
            type: 'Object',
            default: {
                id: false,
            },
        },
    },

    edit: class extends Component {
        constructor(props) {
            super(...arguments);
            this.props = props;
        }

        render() {
            const { attributes, setAttributes } = this.props;
            let classNameBase = getBlockDefaultClassName('mhm/bravo');
            let className = this.props.className;

            if (
                !!attributes.image.id &&
                parseInt(attributes.image.attributes.width) <
                    parseInt(attributes.image.attributes.height)
            ) {
                className += ` ${classNameBase}--tall`;
            }

            return [
                <section className={className}>
                    <div className={`${classNameBase}__inner`}>
                        <div className={`${classNameBase}__content`}>
                            <header className={`${classNameBase}__header`}>
                                <BlockTitle
                                    className={`${classNameBase}__title`}
                                    title={attributes.title}
                                    setAttributes={setAttributes}
                                />
                            </header>
                            <RichText
                                tagName='div'
                                className={`${classNameBase}__text`}
                                allowedFormats={['core/link']}
                                placeholder={_x('Add text', '', 'sha')}
                                multiline='p'
                                value={attributes.text}
                                keepPlaceholderOnFocus={true}
                                onChange={value => {
                                    setAttributes({ text: value });
                                }}
                            />
                        </div>
                        <LazyImageSelector
                            attributes={attributes}
                            className={`${classNameBase}__figure`}
                            image={attributes.image}
                            setAttributes={setAttributes}
                        />
                    </div>
                </section>,
            ];
        }
    },
    save({ attributes }) {
        let className = getBlockDefaultClassName('mhm/bravo');
        const classNameBase = getBlockDefaultClassName('mhm/bravo');

        if (
            !!attributes.image.id &&
            parseInt(attributes.image.attributes.width) <
                parseInt(attributes.image.attributes.height)
        ) {
            className += ` ${className}--tall`;
        }

        return (
            <section className={className}>
                <div className={`${classNameBase}__inner`}>
                    <div className={`${classNameBase}__content`}>
                        <header className={`${classNameBase}__header`}>
                            <RichText.Content
                                tagName='h2'
                                className={`${classNameBase}__title`}
                                value={attributes.title}
                            />
                        </header>
                        {!!attributes.text && attributes.text !== '<p></p>' && (
                            <RichText.Content
                                tagName='div'
                                className={`${classNameBase}__text`}
                                value={attributes.text}
                            />
                        )}
                    </div>
                    {attributes.image && attributes.image.id && (
                        <LazyImage
                            className={`${classNameBase}__figure`}
                            image={attributes.image}
                            background={false}
                            admin={false}
                        />
                    )}
                </div>
            </section>
        );
    },
});
