import { src, dest } from 'gulp';
import glob from 'glob';
import rename from 'gulp-rename';
import path from 'path';
import gulpWebpack from 'webpack-stream';

const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');

export const task = config => {
    return new Promise(resolve => {
        const taskPath = `${config.assetsSrc}/scripts/**/*.js`,
            files = glob.sync(taskPath),
            entries = {};

        files.forEach(file => {
            if (!path.basename(file).match(/^_/)) {
                const folders = path.dirname(file).split('/');
                const folder_last = folders[folders.length - 1];

                // Ignore bottom-level folders which start with underscore
                // and ignore anything which is in the _components folder.
                if (!folder_last.match(/^_/) && !path.dirname(file).match(/_components/)) {
                    entries[`${folders[3]}_${folder_last}`] = file; // MyBlock_editor.js || MyBlock_view.js
                }
            }
        });

        src([taskPath])
            .pipe(
                gulpWebpack({
                    entry: entries,
                    mode: 'production',
                    module: {
                        rules: [
                            {
                                test: /\.js$/,
                                exclude: /node_modules/,
                                loader: 'babel-loader',
                            },
                            {
                                test: /\.s?css$/i,
                                use: ['style-loader', 'css-loader', 'sass-loader'],
                            },
                            {
                                test: /\.svg$/i,
                                issuer: /\.[jt]sx?$/,
                                use: [{ loader: '@svgr/webpack', options: { icon: true } }],
                            },
                        ],
                    },
                    output: {
                        filename: '[name].js',
                    },
                    externals: {
                        jquery: 'jQuery',
                    },
                    plugins: [new DependencyExtractionWebpackPlugin()],
                    // resolve: {
                    //     alias: {
                    //         '@sharedcomponents': path.resolve(
                    //             __dirname,
                    //             config.scriptsComponentsDir
                    //         ),
                    //     },
                    // },
                })
            )
            .on('error', config.errorLog)
            .pipe(
                rename(function (path) {
                    const basename_parts = path.basename.split('_'),
                        target_basename = basename_parts[1],
                        target_basefolder = basename_parts[0],
                        path_new = {
                            dirname: config.assetsDist + target_basefolder,
                            basename: target_basename,
                            extname: path.extname,
                        };
                    return path_new;
                })
            )
            .pipe(dest('./'));
        resolve();
    });
};
