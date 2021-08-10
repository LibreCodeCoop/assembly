<template>
	<Content appName="assembly">
		<AppNavigation>
			<template #list>
				<AppNavigationItem
					:to="{ name: 'room' }"
					:title="t('assembly', 'Room')"
					icon="icon-video"
				/>
				<AppNavigationItem
					:to="{ name: 'votation' }"
					:title="t('assembly', 'Votations')"
					icon="icon-category-organization"
				/>
				<AppNavigationItem
					:to="{ name: 'results' }"
					:title="t('assembly', 'Results')"
					icon="icon-clippy"
				/>
			</template>
		</AppNavigation>
		<router-view />
	</Content>
</template>

<script lang="ts">
import Vue from "vue";
import Content from "@nextcloud/vue/dist/Components/Content";
import AppNavigation from "@nextcloud/vue/dist/Components/AppNavigation";
import AppNavigationItem from "@nextcloud/vue/dist/Components/AppNavigationItem";
export default Vue.extend({
	components: {
		Content,
		AppNavigation,
		AppNavigationItem,
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
