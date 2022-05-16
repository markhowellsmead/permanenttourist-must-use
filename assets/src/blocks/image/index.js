import { RichText } from '@wordpress/block-editor';
import { _x } from '@wordpress/i18n';
import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import classnames from 'classnames';

import FigureWithImage from '../_components/FigureWithImage';
import edit from './edit';
import transforms from './transforms';

registerBlockType('mhm/image', {
    title: _x('Custom image block', 'Block title', 'sha'),
    icon: 'format-image',
    category: 'widgets',
    keywords: ['image', 'gallery'],
    supports: {
        align: ['wide', 'full'],
        html: false,
        inserter: true,
    },
    attributes: {
        image: {
            type: 'Object',
            default: {
                id: false,
            },
        },
        focalPoint: {
            type: 'Object',
            default: {
                x: 0.5,
                y: 0.5,
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
        text_shadow: {
            type: 'boolean',
        },
    },
    transforms,
    edit,
    save({ attributes }) {
        let className = getBlockDefaultClassName('mhm/image');
        const classNameBase = getBlockDefaultClassName('mhm/image');

        const { figcaption, focalPoint, image, textOpacity, textColor, ratio, text_shadow } =
            attributes;

        const classNameFigure = classnames({
            [`${classNameBase}__figcaption`]: true,
            [`${classNameBase}__figcaption--textshadow`]: text_shadow,
        });

        if (!!image.id && parseInt(image.attributes.width) < parseInt(image.attributes.height)) {
            className += ` ${className}--tall`;
        }

        let textStyle = {
            opacity: !!textOpacity ? textOpacity / 100 : 0,
        };

        if (!!textColor) {
            textStyle.color = textColor;
        }

        return (
            <section className={`${className} ${ratio}`}>
                {!!image.id && (
                    <FigureWithImage
                        classNameBase={classNameBase}
                        image={image}
                        focalPoint={focalPoint}
                        lazy={true}
                    />
                )}
                {!!figcaption && (
                    <RichText.Content
                        style={textStyle}
                        tagName='figcaption'
                        className={classNameFigure}
                        value={figcaption}
                    />
                )}
            </section>
        );
    },
});
