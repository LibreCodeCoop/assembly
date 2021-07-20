//eslint-disable-next-line
const TerserPlugin = require("terser-webpack-plugin");

const appName = process.env.npm_package_name;
const buildMode = process.env.NODE_ENV;
const isDev = buildMode === "development";

module.exports = {
	devServer: {},

	outputDir: "./js",

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
