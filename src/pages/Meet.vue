<template>
	<div class="container-meet">
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
		<Modal v-show="showForm" :title="t('assembly', 'Votações')" v-if="form">
			<div class="container-modal">
				<header>
					<h1>{{ form.title }}</h1>
					<span>{{ form.description }}</span>
				</header>
				<form ref="form" @submit.prevent="onSubmit">
					<ul>
						<li
							class="questions"
							v-for="question in form.questions"
							:key="question.id"
						>
							<h1>{{ question.text }}</h1>
							<ul>
								<li
									v-for="(option, index) in question.options"
									:key="index"
									class="options"
								>
									<input
										:id="option.id"
										type="radio"
										:name="question.id"
										v-model="answers[question.id]"
										:value="option.id"
									/>
									<label :for="option.id">{{
										option.text
									}}</label>
								</li>
							</ul>
						</li>
					</ul>
					<button type="submit">{{ t("assembly", "Votar") }}</button>
				</form>
			</div>
		</Modal>
	</div>
</template>
<script lang="ts">
import Vue from "vue";
import store from "@/store";
import axios from "@nextcloud/axios";
import { Modal } from "@nextcloud/vue";
import Meet from "@/components/Meet/Meet.vue";
import { AddVotation, AddVotations } from "@/store/modules/votations/types";
import { generateOcsUrl, generateUrl } from "@nextcloud/router";
import { AddForm } from "@/store/modules/form/types";
import { IVotation } from "@/entities/Votations";
export default Vue.extend({
	components: { Meet, Modal },
	data: () => ({
		showMeet: false,
		viewbtn: true,
		showVoted: false,
		showVotations: false,
		questionSelectedOption: null,
		answers: {},
	}),
	computed: {
		url() {
			return store.state.meet.meet.meetUrl;
		},
		form() {
			return store.state.form.form;
		},
		showForm() {
			return store.state.votations.votations.some(
				(elem) => elem.status === "enabled"
			);
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
	},
	methods: {
		toggleVotationsSide() {
			this.showVotations = !this.showVotations;
		},

		async onSubmit() {
			this.loading = true;
			try {
				await axios.post(
					generateOcsUrl("apps/forms/api/v1.1/submission/insert"),
					{
						formId: this.form.id,
						answers: this.answers,
					}
				);
				this.closeModal();
				// this.getData();
			} catch (error) {
				console.error(error);
			}
		},

		async getForm(id) {
			const response = await axios.get(
				generateOcsUrl(`/apps/forms/api/v1/form/${id}`)
			);
			await store.commit(new AddForm(response.data.ocs.data));
		},

		async getData() {
			const response = await axios.get(
				generateUrl("/apps/assembly/api/v1/pools")
			);
			const pools: IVotation[] = response.data.pools;
			pools.some((elem) => {
				if (elem.status === "enabled") {
					this.getForm(elem.formId);
				}
			});
			await store.commit(new AddVotations(pools));
		},

		async fetchMockVotations() {
			const votations = [
				{
					title: "Algum Title",
					available: 19,
					description: "Form Desc",
					formId: 5,
					status: "enabled",
					finishedAt: "2021-03-03 11:30:20",
					voted: true,
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
					formId: 4,
					status: "disabled",
					finishedAt: "2021-03-03 11:30:20",
					voted: false,
					responses: [
						{
							text: "Response TT",
							total: 39,
						},
					],
				},
			];

			votations.some((elem) => {
				if (elem.status === "enabled") {
					this.getForm(elem.formId);
				}
			});

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
<style lang="scss">
.modal-container {
	width: 50% !important;
	min-height: 50% !important;
}
.container-meet {
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
.container-modal {
	width: 100%;
	display: flex;
	min-width: 50%;
	flex-direction: column;
	justify-content: center;
	align-items: center;

	header {
		width: 80%;
		margin-top: 20px;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: flex-start;

		h1 {
			font-size: 1rem;
			font-weight: bold;

			&::first-letter {
				text-transform: uppercase;
			}
		}
		span {
			font-size: 0.8rem;
			opacity: 0.5;
		}
	}
	form {
		width: 80%;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;

		ul {
			width: 100%;
			height: 100%;
			overflow: scroll;
			display: flex;
			flex-direction: column;
			max-height: 350px;
			align-items: flex-start;
			li.questions:not(:last-child) {
				margin-bottom: 50px;
			}
			li {
				h1 {
					font-size: 0.9rem;
					font-weight: bold;

					&::first-letter {
						text-transform: uppercase;
					}
				}
				ul {
					li.options {
						display: flex;
						flex-direction: row;
						align-items: center;
						input {
							margin-right: 10px;
						}
						label {
							&::first-letter {
								text-transform: uppercase;
							}
						}
					}
				}
				&:last-child {
					margin-bottom: 10px;
				}
			}
		}
	}
}
</style>
