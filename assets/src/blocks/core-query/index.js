/**
 * Original https://github.com/ryanwelcher/advanced-query-loop/blob/trunk/src/components/post-order-controls.js
 * by Ryan Welcher
 */

import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { PostOrderControls } from './post-order-controls';

const withAdvancedQueryControls = BlockEdit => props => {
    // If the is the correct variation, add the custom controls.
    // If the inherit prop is false, add all the controls.
    const { attributes } = props;

    if (props.name !== 'core/query' || attributes?.query?.inherit !== false) {
        return <BlockEdit {...props} />;
    }

    return (
        <>
            <BlockEdit {...props} />
            <InspectorControls>
                <PanelBody title={__('Additional Query Settings', 'permanenttourist-must-use')}>
                    <PostOrderControls {...props} />
                </PanelBody>
            </InspectorControls>
        </>
    );
};

addFilter('editor.BlockEdit', 'core/query', withAdvancedQueryControls);
