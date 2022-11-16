const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  // directory where compiled assets will be stored
  .setOutputPath('public/dist/')
  // public path used by the web server to access the output path
  .setPublicPath('/dist')
  // only needed for CDN's or sub-directory deploy
  //.setManifestKeyPrefix('build/')

  /*
   * ENTRY CONFIG
   */
  .addEntry('admin', './assets/js/admin.js')
  .addEntry('app', './assets/js/app.js')

  .addPlugin(new CopyWebpackPlugin([
    // Copy the skins from tinymce to the build/skins directory
    {from: 'node_modules/tinymce/skins', to: 'skins'},
  ]))

  .addExternals({
    jquery: 'jQuery'
  })

  // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
  //.splitEntryChunks()

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .enableSingleRuntimeChunk()

  /*
   * FEATURE CONFIG
   *
   * Enable & configure other features below. For a full
   * list of features, see:
   * https://symfony.com/doc/current/frontend.html#adding-more-features
   */
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications(!Encore.isProduction())
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning()

  // enables @babel/preset-env polyfills
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = 'usage';
    config.corejs = 3;
  })

  // enables Sass/SCSS support
  .enableSassLoader(function (options) {
    options.sassOptions.includePaths = [
      'node_modules',
    ];
  })

  // uncomment if you're having problems with a jQuery plugin
  .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
