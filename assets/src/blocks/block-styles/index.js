import domReady from '@wordpress/dom-ready';
import { registerBlockStyle } from '@wordpress/blocks';

domReady(() => {
    registerBlockStyle('core/cover', {
        name: 'full-height',
        label: 'Full height',
    });

    registerBlockStyle('core/columns', {
        name: 'narrow-gap',
        label: 'Narrow gap',
    });

    registerBlockStyle('core/heading', {
        name: 'large',
        label: 'L',
    });
    registerBlockStyle('core/heading', {
        name: 'xlarge',
        label: 'XL',
    });
    registerBlockStyle('core/paragraph', {
        name: 'lead',
        label: 'Lead text',
    });
    registerBlockStyle('shb/video-bar', {
        name: 'fullheight',
        label: 'Full screen height',
    });

    registerBlockStyle('core/group', {
        name: 'padding',
        label: 'Vertical padding',
    });

    registerBlockStyle('core/group', {
        name: 'padding--medium',
        label: 'Vertical padding (medium)',
    });

    registerBlockStyle('core/group', {
        name: 'padding--large',
        label: 'Vertical padding (large)',
    });

    registerBlockStyle('core/group', {
        name: 'padding--xlarge',
        label: 'Vertical padding (xlarge)',
    });

    registerBlockStyle('core/button', {
        name: 'xsmall',
        label: 'x-small',
    });

    registerBlockStyle('core/button', {
        name: 'small',
        label: 'Small',
    });

    registerBlockStyle('core/button', {
        name: 'medium',
        label: 'Medium',
    });

    registerBlockStyle('core/button', {
        name: 'large',
        label: 'Large',
    });

    registerBlockStyle('core/image', {
        name: 'webcam',
        label: 'Webcam (3x2)',
    });
});
