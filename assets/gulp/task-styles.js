import { src, dest } from 'gulp';

import cleanCSS from 'gulp-clean-css';
import filter from 'gulp-filter';
import sassImportJson from 'gulp-sass-import-json';
import autoprefixer from 'gulp-autoprefixer';
import rename from 'gulp-rename';
import sourcemaps from 'gulp-sourcemaps';
const sass = require('gulp-sass')(require('sass'));

export const task = config => {
    return (
        src(config.assetsSrc + '/styles/**/*.scss')
            .pipe(sassImportJson({ cache: false, isScss: true }))
            .pipe(sourcemaps.init())
            .pipe(
                sass({
                    includePaths: ['./node_modules/'],
                }).on('error', sass.logError)
            )
            .pipe(sourcemaps.write({ includeContent: false }))
            .pipe(sourcemaps.init({ loadMaps: true }))
            .pipe(autoprefixer())
            .pipe(dest(config.assetsDist + 'styles/'))
            .pipe(sourcemaps.write('.'))
            .on('error', config.errorLog)
            // minify
            .pipe(cleanCSS())
            .pipe(
                rename({
                    suffix: '.min',
                })
            )
            .on('error', config.errorLog)
            .pipe(dest(config.assetsDist + 'styles/'))
            .pipe(filter('**/*.css'))
    );
};
