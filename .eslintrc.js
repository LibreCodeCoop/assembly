module.exports = {
	root: true,
	env: {
		node: true,
	},
	extends: [
		"nextcloud",
		"plugin:vue/essential",
		"eslint:recommended",
		"@vue/typescript/recommended",
		"@vue/prettier",
		"@vue/prettier/@typescript-eslint",
	],
	parserOptions: {
		ecmaVersion: 2020,
	},
	rules: {
		"no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
		"no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off",
		"@typescript-eslint/no-explicit-any": [
			"error",
			{ ignoreRestArgs: true },
		],
		"prettier/prettier": ["error", { singleQuote: false, parser: "flow" }],
		"node/no-missing-import": "off",
	},
	settings: {
		"import/resolver": {
			alias: {
				map: [
					["@", "./src"],
					["~", "./src"],
				],
				extensions: [".js", ".vue", ".ts", ".tsx"],
			},
		},
	},
	overrides: [
		{
			files: [
				"**/__tests__/*.{j,t}s?(x)",
				"**/tests/unit/**/*.spec.{j,t}s?(x)",
			],
			env: {
				jest: true,
			},
		},
	],
};
