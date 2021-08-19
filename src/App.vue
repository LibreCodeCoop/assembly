<template>
	<Content appName="assembly">
		<AppContent :class="{ 'icon-loading': loading }">
			<router-view v-show="!loading" :loading.sync="loading" />
			<EmptyContent v-show="isRoot" class="emp-content">
				<template #desc>
					<p>
						{{ t("assembly", "Tavola app for Nextcloud.") }}
					</p>
				</template>
			</EmptyContent>
		</AppContent>
	</Content>
</template>

<script lang="ts">
import Vue from "vue";
import { Content, AppContent, EmptyContent } from "@nextcloud/vue";
export default Vue.extend({
	components: {
		AppContent,
		Content,
		EmptyContent,
	},
	data: () => ({
		loading: false,
	}),
	computed: {
		isRoot() {
			return this.$route.path === "/";
		},
	},
});
</script>

<style lang="scss">
#app {
	font-family: Avenir, Helvetica, Arial, sans-serif;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
	text-align: center;
	color: #2c3e50;
}

#nav {
	padding: 30px;

	a {
		font-weight: bold;
		color: #2c3e50;

		&.router-link-exact-active {
			color: #42b983;
		}
	}
}
</style>
