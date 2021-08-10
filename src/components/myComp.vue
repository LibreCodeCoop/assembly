<template>
	<h1>{{ newData }}</h1>
</template>

<script lang="ts">
import Vue from "vue";
export default Vue.extend({
	props: {
		myFile: {
			type: String,
			required: false,
			default: "",
		},
		myArray: {
			type: Array,
			required: false,
			default: () => [],
		},
	},
	data: () => ({
		newData: "" as string,
	}),
});
</script>
