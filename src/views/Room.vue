<template>
	<div class="container">
		<Meet :url="url" />
	</div>
</template>
<script lang="ts">
import Vue from "vue";
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";

import Meet from "@/components/Meet";

export default Vue.extend({
	name: "Room",
	components: {
		Meet,
	},
	data: () => ({
		url: "",
	}),
	created() {
		this.getData();
	},
	methods: {
		async getData() {
			try {
				const response = await axios.get(
					generateUrl("/apps/assembly/api/v1/dashboard")
				);
				this.url = response.data.meetUrl;
				console.info(response);
			} catch (err) {
				console.error(err.response);
			}
		},
	},
});
</script>

<style lang="scss" scoped>
.container {
	display: flex;
	height: 100%;
}
</style>
