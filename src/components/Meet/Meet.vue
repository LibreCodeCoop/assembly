<template>
	<JitsiMeet
		ref="jitsiref"
		domain="meet.librecode.coop"
		:options="jitsiOptions"
	/>
</template>

<script lang="ts">
import Vue from "vue";
import { getCurrentUser } from "@nextcloud/auth";
import JitsiMeet from "@/components/jitsiMeet/JitsiMeet.vue";

export default Vue.extend({
	name: "Meet",

	props: {
		url: {
			type: String,
			required: true,
		},
	},

	components: {
		JitsiMeet,
	},
	computed: {
		customUrl() {
			return this.url.split("https://meet.librecode.coop")[1];
		},
		jitsiOptions() {
			return {
				roomName: this.customUrl,
				noSSL: false,
				userInfo: {
					email: "",
					displayName: getCurrentUser().displayName
						? getCurrentUser().displayName
						: getCurrentUser().uid,
				},
				configOverwrite: {
					enableNoisyMicDetection: false,
					removeVideoMenu: {
						disableKick: true,
						disableGrantModerator: true,
					},
					startVideoMuted: 10,
					disableProfile: true,
					prejoinPageEnabled: true,
					enableClosePage: false,
					toolbarButtons: [
						"camera",
						"chat",
						"desktop",
						"etherpad",
						"fullscreen",
						"help",
						"microphone",
						"select-background",
						"stats",
						"tileview",
						"videoquality",
						"toggle-camera",
						"__end",
					],
				},
				interfaceConfigOverwrite: {
					SHOW_JITSI_WATERMARK: false,
					SHOW_WATERMARK_FOR_GUESTS: false,
					SHOW_CHROME_EXTENSION_BANNER: false,
					LANG_DETECTION: false,
				},
				onload: this.onIFrameLoad,
			};
		},
	},
});
</script>
