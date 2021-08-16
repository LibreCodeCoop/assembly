<template>
	<div class="container">
		<div class="meet">
			<button
				v-show="viewbtn"
				class="primary"
				@click="enableMeet"
				v-if="url"
			>
				{{ t("assembly", "Join Meet") }}
			</button>
			<span v-else>{{
				t("assembly", "Wait until time for the meeting")
			}}</span>
			<meet v-if="showMeet" :url="url" />
		</div>
		<div class="votations">
			<div class="questions">votations</div>
			<div class="results">results</div>
		</div>
	</div>
</template>
<script lang="ts">
import Vue from "vue";
import store from "@/store";
import Meet from "@/components/Meet/Meet.vue";
export default Vue.extend({
	components: { Meet },
	data: () => ({
		showMeet: false,
		viewbtn: true,
	}),
	computed: {
		url() {
			return store.state.meet.url;
		},
		votations() {
			return [
				{
					title: "Algum Title",
					available: 19,
					description: "Form Desc",
					id: 123,
					status: "enabled",
					finished_at: "2021-03-03 11:30:20",
					voted: false,
					responses: [
						{
							text: "Response text",
							total: 19,
						},
					],
				},
			];
		},
	},
	methods: {
		enableMeet() {
			this.showMeet = true;
			this.viewbtn = false;
		},
	},
});
</script>
<style lang="scss" scoped>
.container {
	display: flex;
	width: 100%;
	height: 100%;
	flex-direction: column;

	.meet {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		background-color: aqua;
		height: 50%;
	}

	.votations {
		width: 100%;
		height: 50%;
		background-color: blueviolet;

		.questions {
			background-color: chartreuse;
			width: 30%;
			height: 100%;
		}
		.results {
			width: 70%;
			height: 100%;
			background-color: chocolate;
		}
	}
}
</style>
