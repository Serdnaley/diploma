const mix = require('laravel-mix');
const SWPrecacheWebpackPlugin = require('sw-precache-webpack-plugin');

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
    .js('resources/js/app.js', 'public/js/')
    .version()
    .copyDirectory('resources/icons', 'public/icons')
    .copyDirectory('node_modules/element-ui/packages/theme-chalk/src/fonts', 'public/fonts');

// PWA
mix.webpackConfig({
    plugins: [
        new SWPrecacheWebpackPlugin({
            cacheId: 'pwa',
            filename: 'service-worker.js',
            staticFileGlobs: ['public/**/*.{css,eot,svg,ttf,woff,woff2,js,html}'],
            minify: true,
            stripPrefix: 'public/',
            handleFetch: true,
            dynamicUrlToDependencies: {
                '/': ['resources/views/index.blade.php'],
            },
            staticFileGlobsIgnorePatterns: [/\.map$/, /mix-manifest\.json$/, /manifest\.json$/, /service-worker\.js$/],
            navigateFallback: '/',
            runtimeCaching: [
                {
                    urlPattern: /^https:\/\/fonts\.googleapis\.com\//,
                    handler: 'cacheFirst'
                },
                {
                    urlPattern: /^https:\/\/www\.thecocktaildb\.com\/images\/media\/drink\/(\w+)\.jpg/,
                    handler: 'cacheFirst'
                }
            ],
        })
    ]
});
