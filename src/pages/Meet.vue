<template>
	<div class="container">
		<div :class="showVotations === true ? 'meet' : 'meet-100'">
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
		<div v-show="showVotations" class="votations">
			<div class="questions">
				<div v-for="votation in votations" :key="votation.id">
					<h1>{{ votation.title }}</h1>
					<button @click="selectVotation(votation.id)">
						{{ t("assembly", "View") }}
					</button>
				</div>
			</div>
			<div class="results">
				<h1>{{ t("assembly", "Results") }}</h1>
				<div v-show="!voted">
					{{ votation }}
				</div>
				<div v-show="voted">
					<li v-for="vote in votation.responses" :key="vote.text">
						<p>{{ vote.text }}</p>
						<span>{{ vote.total }}</span>
					</li>
				</div>
			</div>
		</div>
		<button
			:class="
				showVotations === true
					? 'icon-triangle-s icons-show btn-position-true'
					: 'icon-triangle-n icons-show btn-position-false'
			"
			@click="toggleVotationsSide"
		></button>
	</div>
</template>
<script lang="ts">
import Vue from "vue";
import axios from "@nextcloud/axios";
import store from "@/store";
import Meet from "@/components/Meet/Meet.vue";
import { AddVotation, AddVotations } from "@/store/modules/votations/types";
import { generateUrl } from "@nextcloud/router";
export default Vue.extend({
	components: { Meet },
	data: () => ({
		showMeet: false,
		viewbtn: true,
		showVoted: false,
		showVotations: false,
	}),
	computed: {
		url() {
			return store.state.meet.meet.meetUrl;
		},
		voted() {
			if (this.showVoted) {
				if (this.votation !== null) {
					if (this.votation.voted) {
						return this.votation.voted;
					}
					return false;
				}
			}
			return false;
		},
		votations() {
			return store.state.votations.votations;
		},
		votation() {
			return store.state.votations.votation;
		},
	},
	created() {
		this.fetchMockVotations();
		this.getData();
	},
	methods: {
		async getData() {
			const response2 = await axios.get(
				generateUrl("apps/forms/api/v1.1/form/1")
			);
			console.info("forms2: ", response2);
		},

		toggleVotationsSide() {
			this.showVotations = !this.showVotations;
		},

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
					title: "Algum Title2",
					available: 19,
					description: "Form Desc",
					id: 124,
					status: "enabled",
					finished_at: "2021-03-03 11:30:20",
					voted: false,
					responses: [
						{
							text: "Response TT",
							total: 39,
						},
					],
				},
			];
			await store.commit(new AddVotations(votations));
		},
		selectVotation(id) {
			this.votations.forEach(async (element) => {
				if (element.id === id) {
					await store.commit(new AddVotation(element));
				}
			});
			this.showVoted = true;
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
	.meet-100 {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
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
			padding: 20px;

			h1 {
				font-weight: bold;
			}
		}
	}

	.icons-show {
		position: absolute;
		bottom: 5px;
		left: 5px;
		min-width: 44px;
		height: 44px;
		border-radius: 22px;
		background-size: 39px;
		background-color: #cecece;
	}
	.btn-position-true {
		bottom: 5px;
	}
	.btn-position-true {
		bottom: 50.2%;
	}
}
</style>
