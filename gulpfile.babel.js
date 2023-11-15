import gulp from 'gulp';

// const rootFolder = process.cwd();

const config = {
    name: 'Site blocks',
    key: 'sht',
    assetsDist: './assets/dist/',
    assetsSrc: './assets/src',
    // scriptsComponentsDir: `${rootFolder}/assets/scripts/_components`,
    blockStylesDist: './src/Blocks/',
    blockStylesSrc: './src/Blocks/**/src/**/styles',
    blockScriptsSrc: './src/Blocks/**/src/**/scripts',
    blockScriptsDist: './src/Blocks/',
    errorLog: function (error) {
        console.log('\x1b[31m%s\x1b[0m', error);
        if (this.emit) {
            this.emit('end');
        }
    },
};

import { task as taskGutenberg } from './assets/gulp/task-gutenberg';
import { task as taskStyles } from './assets/gulp/task-styles';
import { task as taskBlockStyles } from './assets/gulp/task-block-styles';
import { task as taskBlockScripts } from './assets/gulp/task-block-scripts';
import { task as taskScripts } from './assets/gulp/task-scripts';

export const block_scripts = () => taskBlockScripts(config);
export const block_styles = () => taskBlockStyles(config);
export const gutenberg = () => taskGutenberg(config);
export const styles = () => taskStyles(config);
export const scripts = () => taskScripts(config);

export const watch = () => {
    const settings = { usePolling: true, interval: 100 };
    gulp.watch(`${config.assetsSrc}/**/*.{js,jsx}`, settings, gulp.series(gutenberg));
    gulp.watch(`${config.assetsSrc}/scripts/**/*.{js,scss}`, settings, gulp.series(scripts));
    gulp.watch(`${config.assetsSrc}/styles/**/*.scss`, settings, gulp.series(styles));
    gulp.watch(`${config.blockStylesSrc}/*.{scss,css,js}`, settings, gulp.series(block_styles));
    gulp.watch(`${config.blockScriptsSrc}/**/*.{scss,js}`, settings, gulp.series(block_scripts));
};

export const taskDefault = gulp.series(watch);
export default taskDefault;
