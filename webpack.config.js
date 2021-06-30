const Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('src/Resources/public')
    // public path used by the web server to access the output path
    .setPublicPath('/public')

    // entries
    .addEntry('shipping-slot-js', './assets/js/app.js')

    // configuration
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    // organise files
    .configureFilenames({
        js: 'js/[name].js'
    })
;

module.exports = Encore.getWebpackConfig();
