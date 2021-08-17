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
			<div class="questions">
				<div v-for="votation in votations" :key="votation.id">
					<h1>{{ votation.title }}</h1>
					<button>{{ t("assembly", "View") }}</button>
				</div>
			</div>
			<div class="results">
				<h1>{{ t("assembly", "Results") }}</h1>
				<div>s</div>
			</div>
		</div>
	</div>
</template>
<script lang="ts">
import Vue from "vue";
import store from "@/store";
import Meet from "@/components/Meet/Meet.vue";
import { AddVotations } from "@/store/modules/votations/types";
export default Vue.extend({
	components: { Meet },
	data: () => ({
		showMeet: false,
		viewbtn: true,
	}),
	computed: {
		url() {
			return store.state.meet.meet.meetUrl;
		},
		votations() {
			return store.state.votations;
		},
	},
	created() {
		this.fetchMockVotations();
	},
	methods: {
		async fetchMockVotations() {
			const votations = [
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
				{
					title: "Algum Title",
					available: 19,
					description: "Form Desc",
					id: 124,
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
			await store.commit(new AddVotations(votations));
		},
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
		height: 50%;
	}

	.votations {
		display: flex;
		width: 100%;
		height: 50%;
		border-top: 1px solid #cecece;

		.questions {
			width: 30%;
			height: 100%;
			border-right: 1px solid #cecece;

			div {
				display: flex;
				flex-direction: row;
				justify-content: space-between;
				align-items: center;
				margin: 10px;
				border: 1px solid #cecece;
				border-radius: 5px;

				h1 {
					padding: 10px;
				}
			}
		}

		.results {
			width: 70%;
			height: 100%;
		}
	}
}
</style>
