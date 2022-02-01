import { InspectorControls, RichText } from '@wordpress/block-editor';
import { getBlockDefaultClassName } from '@wordpress/blocks';
import classnames from 'classnames';
import {
    SelectControl,
    PanelBody,
    ColorPalette,
    FocalPointPicker,
    RangeControl,
    ToggleControl,
} from '@wordpress/components';
import { select, withSelect } from '@wordpress/data';
import { Fragment, Component } from '@wordpress/element';
import { _x } from '@wordpress/i18n';

import ImageSelectorWithPlaceholder from '../_components/ImageSelectorWithPlaceholder';
import { LazyImage } from '../_components/LazyImage';

import ratios from './ratios';

class Edit extends Component {
    constructor(props) {
        super(...arguments);
        this.props = props;
    }

    render() {
        const { attributes, colors, setAttributes } = this.props;

        const {
            figcaption,
            focalPoint,
            image,
            textOpacity,
            textColor,
            ratio,
            text_shadow,
        } = attributes;

        let classNameBase = getBlockDefaultClassName('mhm/image');
        let className = classNameBase;

        const classNameFigure = classnames({
            [`${classNameBase}__figcaption`]: true,
            [`${classNameBase}__figcaption--textshadow`]: text_shadow,
        });

        if (!!image.id && parseInt(image.attributes.width) < parseInt(image.attributes.height)) {
            className += ` ${classNameBase}--tall`;
        }

        let imageData = !!image.id ? select('core').getMedia(image.id) : null;

        let textStyle = {
            opacity: !!textOpacity ? textOpacity / 100 : 0,
        };

        if (!!textColor) {
            textStyle.color = textColor;
        }

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title='Layout settings' initialOpen={true}>
                        <SelectControl
                            label='Select image proportions'
                            value={ratio}
                            options={ratios}
                            onChange={ratio => {
                                setAttributes({ ratio });
                            }}
                        />
                        {imageData && imageData.media_details && (
                            <FocalPointPicker
                                url={imageData.media_details.sizes.full.source_url}
                                dimensions={{
                                    width: imageData.media_details.sizes.full.width,
                                    height: imageData.media_details.sizes.full.height,
                                }}
                                value={focalPoint}
                                onChange={focalPoint => setAttributes({ focalPoint })}
                            />
                        )}
                    </PanelBody>
                    <PanelBody
                        title={_x('Colours', 'Domain Gutenberg Block Panel Title', 'sha')}
                        initialOpen={true}
                    >
                        <label class='components-base-control__label'>
                            {_x('Caption colour', 'Domain Gutenberg Block Panel Label', 'sha')}
                        </label>
                        <ColorPalette
                            colors={colors}
                            value={textColor}
                            onChange={textColor => setAttributes({ textColor })}
                        />
                        <RangeControl
                            label={_x('Text transparency', 'Range control label', 'sha')}
                            value={textOpacity}
                            onChange={textOpacity => setAttributes({ textOpacity })}
                            min={0}
                            max={100}
                        />
                        <ToggleControl
                            label={_x('Text shadow', 'ToggleControl label', 'sha')}
                            help={
                                text_shadow
                                    ? _x('Text shadow added.', 'Help text', 'sha')
                                    : _x(
                                          'Adds a text shadow to the image caption.',
                                          'Help text',
                                          'sha'
                                      )
                            }
                            checked={text_shadow}
                            onChange={text_shadow => setAttributes({ text_shadow })}
                        />
                    </PanelBody>
                </InspectorControls>
                <section className={`${className} ${ratio}`}>
                    <ImageSelectorWithPlaceholder
                        attributes={attributes}
                        setAttributes={setAttributes}
                        allowedTypes={['image']}
                        accept={'image'}
                        allowURL={false}
                        labels={{
                            title: _x('Select an image', 'MediaPlaceholder title', 'sha'),
                            replace: _x('Replace image', 'MediaPlaceholder instructions', 'sha'),
                        }}
                    />
                    {!!image.id && (
                        <LazyImage
                            className={`${classNameBase}__figure`}
                            image={image}
                            background={false}
                            admin={true}
                            objectFocalPoint={focalPoint}
                        />
                    )}
                    <RichText
                        style={textStyle}
                        tagName='figcaption'
                        className={classNameFigure}
                        format='string'
                        allowedFormats={['core/link']}
                        formattingControls={[]}
                        placeholder={_x('Optional caption', 'Placeholder text', 'sha')}
                        multiline='br'
                        value={figcaption}
                        keepPlaceholderOnFocus={true}
                        onChange={figcaption => {
                            setAttributes({ figcaption });
                        }}
                    />
                </section>
            </Fragment>
        );
    }
}

export default withSelect((select, props) => {
    let colors = [],
        colorSettings = select('core/editor').getEditorSettings().colors;

    if (colorSettings) {
        colorSettings.map(color => {
            colors.push({ color: color.color, name: color.name });
        });
    }

    return {
        colors: colors,
    };
})(Edit);
