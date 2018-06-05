const mix = require('laravel-mix');
const webpack = require('webpack');

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
  .js('resources/assets/js/admin/app.js', 'js/admin.js')
  .js('resources/assets/js/noAuth/app.js', 'js/noAuth.js')
  .js('resources/assets/js/site/app.js', 'js/site.js')
  .js('resources/assets/js/teacher/app.js', 'js/teacher.js')
  .js('resources/assets/js/shared/ie.js', 'js/ie.js')
  .sass('resources/assets/sass/admin/index.scss', 'css/admin.css')
  .sass('resources/assets/sass/site/index.scss', 'css/site.css')
  .extract([
    'lodash/fp',
    'jquery',
    'vue',
    '_shared/mixins/rest',
    'moment',
  ])
  .version();

mix.webpackConfig({
  output: {
    filename: '[name].js',
    chunkFilename: 'js/[name].js',
    publicPath: '/',
  },
  resolve: {
    alias: {
      _assets: path.join(__dirname, 'resources', 'assets'),
      _admin: path.join(__dirname, 'resources', 'assets', 'js', 'admin'),
      _noAuth: path.join(__dirname, 'resources', 'assets', 'js', 'noAuth'),
      _site: path.join(__dirname, 'resources', 'assets', 'js', 'site'),
      _teacher: path.join(__dirname, 'resources', 'assets', 'js', 'teacher'),
      _shared: path.join(__dirname, 'resources', 'assets', 'js', 'shared'),
      _components:   path.join(__dirname, 'resources', 'assets', 'js', 'shared', 'components'),
    }
  },
  plugins: [
    new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /ru/)
  ],
});
