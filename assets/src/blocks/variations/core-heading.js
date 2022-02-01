import { registerBlockVariation } from '@wordpress/blocks';

console.log('here123');

registerBlockVariation('core/heading', {
    name: 'sht/h1',
    title: 'H1',
    attributes: { level: 1 },
});

registerBlockVariation('core/heading', {
    name: 'sht/h2',
    title: 'H2',
    attributes: { level: 2 },
});

registerBlockVariation('core/heading', {
    name: 'sht/h3',
    title: 'H3',
    attributes: { level: 3 },
});

registerBlockVariation('core/heading', {
    name: 'sht/h4',
    title: 'H4',
    attributes: { level: 4 },
});

registerBlockVariation('core/heading', {
    name: 'sht/h5',
    title: 'H5',
    attributes: { level: 5 },
});

registerBlockVariation('core/heading', {
    name: 'sht/h6',
    title: 'H6',
    attributes: { level: 6 },
});
