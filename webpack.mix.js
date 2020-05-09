const mix = require('laravel-mix');
const workboxPlugin = require('workbox-webpack-plugin');

const packageJson = require('./package.json');
const dependencies = Object.keys(packageJson.dependencies);

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .extract(dependencies)
    .js('resources/js/app.js', 'public/js/')
    .copyDirectory('resources/favicons', 'public/favicons')
    .copyDirectory('node_modules/element-ui/packages/theme-chalk/src/fonts', 'public/fonts')
    .webpackConfig({
        plugins: [
            new workboxPlugin({
                globDirectory: `${__dirname}/public`,
                globPatterns: [
                    '**/*.{html,css,js}',
                    'fonts/**/*.*'
                ],
                swDest: path.join(`${__dirname}/public`, 'sw.js'),
                clientsClaim: true,
                skipWaiting: true,
                runtimeCaching: [
                    {
                        urlPattern: new RegExp(`${process.env.APP_URL}`),
                        handler: 'networkFirst',
                        options: {
                            cacheName: `${process.env.APP_NAME}-${process.env.APP_ENV}`
                        }
                    },
                    {
                        urlPattern: new RegExp('https://fonts.(googleapis|gstatic).com'),
                        handler: 'cacheFirst',
                        options: {
                            cacheName: 'google-fonts'
                        }
                    }
                ]
            }),
        ]
    });
