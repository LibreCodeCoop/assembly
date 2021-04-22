const { merge } = require('webpack-merge');
const path = require('path');
const webpackConfig = require('@nextcloud/webpack-vue-config');

const config = {
  mode: 'development',
  devtool: 'cheap-source-map',
};

module.exports = merge(config, webpackConfig);
