const Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('src/Resources/public')
    // public path used by the web server to access the output path
    .setPublicPath('/public')

    // entries
    .addEntry('shipping-slot-js', './assets/js/app.js')
    .addStyleEntry('shipping-slot-css', './assets/css/app.scss')

    // configuration
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    // enables Sass/SCSS support
    .enableSassLoader()
    // enables PostCSS support
    .enablePostCssLoader()

    // organise files
    .configureFilenames({
        js: 'js/[name].js',
        css: 'css/[name].css',
    })
;

module.exports = Encore.getWebpackConfig();
