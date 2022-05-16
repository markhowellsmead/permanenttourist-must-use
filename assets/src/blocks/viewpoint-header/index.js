import { InnerBlocks, RichText } from '@wordpress/block-editor';
import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import { _x } from '@wordpress/i18n';

import FigureWithImage from '../_components/FigureWithImage';
import edit from './edit';

registerBlockType('mhm/viewpoint-header', {
    title: _x('Viewpoint header', 'Block title', 'sha'),
    icon: 'layout',
    category: 'widgets',
    keywords: [_x('Header', 'Gutenberg block keyword', 'sha')],
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
        align: {
            type: 'string',
            default: 'full',
        },
        focalPoint: {
            type: 'Object',
            default: {
                x: 0.5,
                y: 0.5,
            },
        },
        image: {
            type: 'Object',
            default: {
                id: false,
            },
        },
        ratio: {
            type: 'string',
            default: 'is-aspect--3x2',
        },
        figcaption: {
            type: 'string',
            default: '',
        },
        textColor: {
            type: 'string',
            default: '',
        },
        textOpacity: {
            type: 'Number',
            default: 100,
        },
    },
    edit,
    save({ attributes }) {
        let className = getBlockDefaultClassName('mhm/viewpoint-header');
        const classNameBase = getBlockDefaultClassName('mhm/viewpoint-header');

        let textStyle = {
            opacity: !!attributes.textOpacity ? attributes.textOpacity / 100 : 0,
        };

        if (!!attributes.textColor) {
            textStyle.color = attributes.textColor;
        }

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
                        <InnerBlocks.Content />
                    </div>
                    {!!attributes.image.id && (
                        <div className={`${className}__figurewrap ${attributes.ratio}`}>
                            <FigureWithImage
                                classNameBase={classNameBase}
                                image={attributes.image}
                                focalPoint={focalPoint}
                                lazy={true}
                            />
                            {!!attributes.figcaption && (
                                <RichText.Content
                                    style={textStyle}
                                    tagName='figcaption'
                                    className={`${classNameBase}__figcaption`}
                                    value={attributes.figcaption}
                                />
                            )}
                        </div>
                    )}
                </div>
            </section>
        );
    },
});
