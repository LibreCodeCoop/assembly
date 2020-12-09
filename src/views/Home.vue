<template>
	<Content app-name="assembly">
		<a href="#" @click.stop.prevent="showList">Teste</a>
	</Content>
</template>

<script>
import Content from '@nextcloud/vue/dist/Components/Content'

export default {
	name: 'Home',

	components: {
		Content,
	},

	methods: {},
}
</script>
