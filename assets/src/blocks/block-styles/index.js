import domReady from '@wordpress/dom-ready';
import { registerBlockStyle } from '@wordpress/blocks';

domReady(() => {
    registerBlockStyle('shb/video-bar', {
        name: 'fullheight',
        label: 'Full screen height',
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

    registerBlockStyle('core/image', {
        name: '16-9',
        label: '16x9',
    });
});
