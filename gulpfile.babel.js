import gulp from 'gulp';

const config = {
    name: 'Site blocks',
    key: 'sht',
    assetsDist: './assets/dist/',
    assetsSrc: './assets/src',
    blockStylesDist: './src/Blocks/',
    blockStylesSrc: './src/Blocks/**/src/**/styles',
    errorLog: function (error) {
        console.log('\x1b[31m%s\x1b[0m', error);
        if (this.emit) {
            this.emit('end');
        }
    },
};

import { task as taskGutenberg } from './assets/gulp/task-gutenberg';
import { task as taskBlockStyles } from './assets/gulp/task-block-styles';

export const block_styles = () => taskBlockStyles(config);
export const gutenberg = () => taskGutenberg(config);

export const watch = () => {
    const settings = { usePolling: true, interval: 100 };
    gulp.watch(`${config.assetsSrc}/**/*.{js,jsx}`, settings, gulp.series(gutenberg));
    gulp.watch(`${config.blockStylesSrc}/*.{scss,css,js}`, settings, gulp.series(block_styles));
};

export const taskDefault = gulp.series(watch);
export default taskDefault;
