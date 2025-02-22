import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow, SelectControl, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { _x } from '@wordpress/i18n';
import { Image } from './_image.js';
import { contentStylesCalc, innerStylesCalc } from './_styles.js';

import block_json from '../../../block.json';
const { name: block_name } = block_json;

const classNameBase = getBlockDefaultClassName(block_name);

registerBlockType(block_name, {
    edit: props => {
        const blockProps = useBlockProps();
        const { attributes, setAttributes } = props;
        const { imageSize, linkText, postId, style } = attributes;

        const innerStyles = innerStylesCalc(style),
            contentStyles = contentStylesCalc(style);

        const pageData = useSelect(select => {
            return select('core').getEntityRecord('postType', 'page', postId);
        });

        const presetImageSizes = useSelect(select => {
            return select('core/editor').getEditorSettings().imageSizes;
        });

        const pages = useSelect(select => {
            return select('core').getEntityRecords('postType', 'page', {
                per_page: -1,
                order: 'asc',
                orderby: 'title',
            });
        });

        const selectPages = [
            {
                label: 'Select a page',
                value: 0,
            },
        ];

        if (pages?.length) {
            pages.forEach(page => {
                selectPages.push({
                    label: page.title.rendered,
                    value: page.id,
                });
            });
        }

        return (
            <>
                <InspectorControls>
                    <PanelBody title={_x('Settings', 'Block settings', 'pt-must-use')}>
                        <PanelRow>
                            {!pages?.length && <Spinner />}
                            {!!pages?.length && (
                                <SelectControl
                                    label={_x('Choose page', 'Block setting', 'pt-must-use')}
                                    value={postId}
                                    options={selectPages}
                                    onChange={postId => setAttributes({ postId })}
                                />
                            )}
                        </PanelRow>
                        {!!presetImageSizes?.length && (
                            <PanelRow>
                                <SelectControl
                                    label={_x('Image size', 'Block setting', 'pt-must-use')}
                                    value={imageSize}
                                    options={presetImageSizes.map(size => ({
                                        label: size.name,
                                        value: size.slug,
                                    }))}
                                    onChange={imageSize => setAttributes({ imageSize })}
                                />
                            </PanelRow>
                        )}
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    {!postId && (
                        <div className='c-editormessage'>
                            {_x('No page selected', 'Block placeholder', 'pt-must-use')}
                        </div>
                    )}
                    {!!postId && !pageData && (
                        <div className='c-editormessage'>
                            <div className='c-editormessage__row'>
                                <Spinner />
                                <span>
                                    {_x('Loading page data...', 'Block placeholder', 'pt-must-use')}
                                </span>
                            </div>
                        </div>
                    )}
                    {pageData && (
                        <div className={`${classNameBase}__inner`} style={innerStyles}>
                            <div className={`${classNameBase}__content`} style={contentStyles}>
                                <h2 className={`${classNameBase}__title`}>
                                    {pageData.title.rendered}
                                </h2>
                                {pageData.excerpt.rendered && (
                                    <div
                                        className={`${classNameBase}__excerpt`}
                                        dangerouslySetInnerHTML={{
                                            __html: pageData.excerpt.rendered,
                                        }}
                                    />
                                )}
                                <div
                                    className={`${classNameBase}__link-wrapper wp-block-button is-style-with-arrow-right has-smaller-font-size`}
                                >
                                    <RichText
                                        placeholder={_x(
                                            'Add link text…',
                                            'Block placeholder',
                                            'pt-must-use'
                                        )}
                                        tagName='div'
                                        className={`${classNameBase}__link wp-block-button__link has-transparent-background-color has-background wp-element-button`}
                                        value={linkText}
                                        onChange={linkText => setAttributes({ linkText })}
                                    />
                                </div>
                            </div>
                            <Image props={{ classNameBase, postId, pageData, imageSize }} />
                        </div>
                    )}
                </div>
            </>
        );
    },
});
