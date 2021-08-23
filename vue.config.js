// eslint-disable-next-line
const TerserPlugin = require("terser-webpack-plugin");
// eslint-disable-next-line
const path = require("path");

const appName = process.env.npm_package_name;
const buildMode = process.env.NODE_ENV;
const isDev = buildMode === "development";

module.exports = {
	devServer: {
		contentBase: "./js",
		public: "./js",
		compress: true,
	},

	publicPath: "./js",

	outputDir: "./js",

	chainWebpack: (config) => {
		config.entry("settings").add("./src/settings.ts").end();
	},

	configureWebpack: (config) => {
		config.output.filename = `${appName}-[name].js?v=[contenthash]`;
		config.output.chunkFilename = `${appName}-[name].js?v=[contenthash]`;
		config.optimization = {
			splitChunks: {
				automaticNameDelimiter: "-",
			},
			minimize: !isDev,
			minimizer: [
				new TerserPlugin({
					terserOptions: {
						output: {
							comments: false,
						},
					},
					extractComments: true,
				}),
			],
		};
	},
};
