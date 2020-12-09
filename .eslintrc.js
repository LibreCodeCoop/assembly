module.exports = {
	globals: {
		appVersion: true
	},
	rules: {
		"node/no-extraneous-import": ["error", {
			"allowModules": [ "@nextcloud/auth" ],
			"resolvePaths": [],
			"tryExtensions": []
		}]
	},
	extends: [
		'@nextcloud',
	]
}
