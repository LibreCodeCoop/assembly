<template>
	<div ref="jitsiContainer"></div>
</template>

<script lang="ts">
import Vue from "vue";

declare global {
	interface Window {
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		JitsiMeetExternalAPI: any;
	}
}

export default Vue.extend({
	props: {
		domain: {
			type: String,
			default: "meet.jit.si",
		},
		options: {
			type: Object,
			default: () => ({}),
		},
	},
	mounted() {
		this.loadScript(`https://${this.domain}/external_api.js`, () => {
			if (!window.JitsiMeetExternalAPI)
				throw new Error("Jitsi Meet API not loaded");
			this.embedJitsiWidget();
		});
	},
	beforeDestroy() {
		this.removeJitsiWidget();
	},
	methods: {
		loadScript(src, cb) {
			const scriptEl = document.createElement("script");
			scriptEl.src = src;
			// scriptEl.async = 1;
			document.querySelector("head").appendChild(scriptEl);
			scriptEl.addEventListener("load", cb);
		},
		embedJitsiWidget() {
			const options = {
				...this.options,
				parentNode: this.$refs.jitsiContainer,
			};
			this.jitsiApi = new window.JitsiMeetExternalAPI(
				this.domain,
				options
			);
		},
		executeCommand(command, ...value) {
			this.jitsiApi.executeCommand(command, ...value);
		},
		addEventListener(event, fn) {
			this.jitsiApi.on(event, fn);
		},
		// Misc
		removeJitsiWidget() {
			if (this.jitsiApi) this.jitsiApi.dispose();
		},
	},
});
</script>

<style lang="scss" scoped>
div {
	height: 100%;
	width: 100%;
}
</style>
