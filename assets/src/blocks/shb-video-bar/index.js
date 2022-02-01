/**
 * The block itself comes in via plugin
 */

import { addFilter } from '@wordpress/hooks';

addFilter('shb-video-bar-colors-to', 'picard.shb-video-bar-colors-to', function (rgba) {
	return 'rgba(0,0,0,1)';
});
