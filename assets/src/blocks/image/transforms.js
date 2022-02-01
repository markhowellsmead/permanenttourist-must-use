import { createBlock } from '@wordpress/blocks';

const transforms = {
    to: [
        {
            type: 'block',
            blocks: ['core/image'], // Block type TO which we can convert
            transform: ({ figcaption, image }) => {
                const newAtts = {};

                if (!!figcaption) {
                    newAtts.caption = figcaption;
                }

                if (!!image && image.org[0]) {
                    newAtts.url = image.org[0];
                    newAtts.id = image.id;
                }

                return createBlock('core/image', newAtts);
            },
        },
    ],
};

export default transforms;
