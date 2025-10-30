import { src, dest } from 'gulp';
import autoprefixer from 'gulp-autoprefixer';
import rename from 'gulp-rename';
import postcss from 'gulp-postcss';
import cssnano from 'cssnano';
const sass = require('gulp-sass')(require('sass'));

export const task = config => {
    return (
        src([`${config.blockStylesSrc}/*.scss`, `!_${config.blockStylesSrc}/*.scss`])
            .pipe(
                sass({
                    includePaths: ['./node_modules/'],
                }).on('error', sass.logError)
            )
            .pipe(autoprefixer())
            .pipe(
                rename(path => ({
                    dirname:
                        config.blockStylesDist.replace('./', '') +
                        path.dirname.replace('/src', '/dist'),
                    basename: path.basename,
                    extname: path.extname,
                }))
            )
            .pipe(dest('./'))
            .on('error', config.errorLog)
            // minify with cssnano
            .pipe(postcss([cssnano()]))
            .on('error', config.errorLog)
            .pipe(rename({ suffix: '.min' }))
            .pipe(dest('./'))
    );
};
