import {
    SelectControl,
    PanelBody,
    ColorPalette,
    FocalPointPicker,
    RangeControl,
} from '@wordpress/components';
import { InnerBlocks, RichText, InspectorControls } from '@wordpress/block-editor';
import { select, withSelect } from '@wordpress/data';
import { Fragment, Component } from '@wordpress/element';
import { _x } from '@wordpress/i18n';
import { getBlockDefaultClassName } from '@wordpress/blocks';

import LazyImageSelector from '../_components/LazyImageSelector';

class Edit extends Component {
    constructor(props) {
        super(...arguments);
        this.props = props;
    }

    render() {
        const { attributes, colors, setAttributes } = this.props;
        let classNameBase = getBlockDefaultClassName('mhm/project-header');
        let className = classNameBase;

        let imageData = !!attributes.image.id ? select('core').getMedia(attributes.image.id) : null;

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
            className += ` ${classNameBase}--tall`;
        }

        let imageFormat = 'full_uncropped';

        return (
            <Fragment>
                {imageData && imageData.media_details && (
                    <InspectorControls>
                        <PanelBody title='Layout settings' initialOpen={true}>
                            <SelectControl
                                label='Select image proportions'
                                value={attributes.ratio}
                                options={[
                                    {
                                        label: '3 x 2',
                                        value: 'is-aspect--3x2',
                                    },
                                    {
                                        label: '4 x 3',
                                        value: 'is-aspect--4x3',
                                    },
                                    {
                                        label: '5 x 4',
                                        value: 'is-aspect--5x4',
                                    },
                                    {
                                        label: '16 x 9',
                                        value: 'is-aspect--16_9',
                                    },
                                    {
                                        label: '2.5:1',
                                        value: 'is-aspect--25_10',
                                    },
                                    {
                                        label: '3:1',
                                        value: 'is-aspect--3x1',
                                    },
                                    {
                                        label: '4:1',
                                        value: 'is-aspect--4x1',
                                    },
                                    {
                                        label: '2 x 3',
                                        value: 'is-aspect--2x3',
                                    },
                                    {
                                        label: '3 x 4',
                                        value: 'is-aspect--3x4',
                                    },
                                    {
                                        label: '4 x 5',
                                        value: 'is-aspect--4x5',
                                    },
                                ]}
                                onChange={value => {
                                    setAttributes({ ratio: value });
                                }}
                            />
                            <FocalPointPicker
                                url={imageData.media_details.sizes.full.source_url}
                                dimensions={{
                                    width: imageData.media_details.sizes.full.width,
                                    height: imageData.media_details.sizes.full.height,
                                }}
                                value={attributes.focalPoint}
                                onChange={newFocalPoint =>
                                    setAttributes({ focalPoint: newFocalPoint })
                                }
                            />
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
                                value={attributes.textColor}
                                onChange={textColor => setAttributes({ textColor })}
                            />
                            <RangeControl
                                label={_x('Text transparency', 'Range control label', 'sha')}
                                value={attributes.textOpacity}
                                onChange={textOpacity => setAttributes({ textOpacity })}
                                min={0}
                                max={100}
                            />
                        </PanelBody>
                    </InspectorControls>
                )}
                <section className={`${className} ${attributes.ratio}`}>
                    <div className={`${classNameBase}__inner`}>
                        <div className={`${classNameBase}__content`}>
                            <InnerBlocks
                                allowedBlocks={(['core/heading'], ['core/paragraph'])}
                                template={[
                                    [
                                        'core/heading',
                                        {
                                            level: 1,
                                        },
                                    ],
                                    ['core/paragraph'],
                                ]}
                            />
                        </div>
                        <div className={`${classNameBase}__figurewrap ${attributes.ratio}`}>
                            <LazyImageSelector
                                attributes={attributes}
                                className={`${classNameBase}__figure`}
                                image={attributes.image}
                                setAttributes={setAttributes}
                                objectFocalPoint={attributes.focalPoint}
                                admin={true}
                                imageFormat={imageFormat}
                            />
                            <RichText
                                style={textStyle}
                                tagName='figcaption'
                                className={`${classNameBase}__figcaption`}
                                format='string'
                                allowedFormats={['core/link']}
                                formattingControls={[]}
                                placeholder={_x('Optional caption', 'Placeholder text', 'sha')}
                                multiline='br'
                                value={attributes.figcaption}
                                keepPlaceholderOnFocus={true}
                                onChange={figcaption => {
                                    setAttributes({ figcaption });
                                }}
                            />
                        </div>
                    </div>
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
