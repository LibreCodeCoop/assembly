<template>
	<div class="container-meet">
		<div :class="showVotations === true ? 'meet' : 'meet-100'">
			<button
				v-show="viewbtn"
				class="primary"
				@click="enableMeet"
				v-if="url"
			>
				{{ t("assembly", "Entrar na assembleia") }}
			</button>
			<span v-else>{{
				t("assembly", "Espere até a hora da Assembleia")
			}}</span>
			<meet v-if="showMeet" :url="url" />
		</div>
		<div v-show="showVotations" class="votations">
			<div class="questions">
				<div v-for="votation in votations" :key="votation.id">
					<h1>{{ votation.title }}</h1>
					<button @click="selectVotation(votation.formId)">
						{{ t("assembly", "Ver") }}
					</button>
				</div>
			</div>
			<div class="results">
				<h1>{{ t("assembly", "Resultados") }}</h1>
				<div class="question">
					<li
						v-for="(question, index) in votation.questions"
						:key="index"
						class="question-votation"
					>
						<p class="question-text">{{ question.text }}</p>
						<ul>
							<li
								v-for="(option, index) in question.options"
								:key="index"
							>
								<p>{{ option.text }}</p>
								<span>{{ option.total }}</span>
							</li>
						</ul>
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
			v-show="haveVotations"
			@click="toggleVotationsSide"
		></button>
		<Modal
			v-show="showModal"
			:title="t('assembly', 'Votações')"
			v-if="form"
			:canClose="false"
		>
			<div class="container-modal">
				<header>
					<h1>{{ form.title }}</h1>
					<span>{{ form.description }}</span>
				</header>
				<form ref="form" @submit.prevent="onSubmit">
					<ul>
						<li
							class="questions"
							v-for="(question, index) in form.questions"
							:key="index"
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
										:value="[option.id]"
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
import { AddVotation, ToggleModal } from "@/store/modules/votations/types";
import { generateOcsUrl } from "@nextcloud/router";
export default Vue.extend({
	components: { Meet, Modal },
	data: () => ({
		showMeet: false,
		viewbtn: true,
		showVoted: false,
		showVotations: false,
		questionSelectedOption: null,
		answers: {},
		modal: false,
	}),
	computed: {
		haveVotations() {
			if (store.state.votations.votations) {
				return store.state.votations.votations.length > 0
					? true
					: false;
			}
			return false;
		},
		url() {
			return store.state.meet.meet.url;
		},
		form() {
			return store.state.form.form;
		},
		showModal() {
			return store.state.votations.isEnabledModal;
		},
		showForm: {
			get: () => {
				return store.state.votations.votations.some(
					(elem) => elem.status === "enabled" && elem.voted === false
				);
			},
			set: (newValue) => {
				return newValue;
			},
		},
		votations() {
			return store.state.votations.votations;
		},
		votation() {
			return store.state.votations.votation;
		},
	},
	created() {
		store.dispatch("getPools", store.state.meet.meet.meetingId);

		setInterval(() => {
			store.dispatch("getPools", store.state.meet.meet.meetingId);
		}, 15000);
	},
	methods: {
		toggleVotationsSide() {
			this.showVotations = !this.showVotations;
		},

		async onSubmit() {
			this.loading = true;
			try {
				await axios.post(
					generateOcsUrl("/apps/forms/api/v1/submission/insert"),
					{
						formId: this.form.id,
						answers: this.answers,
					}
				);
				await store.commit(new ToggleModal(false));
				store.dispatch("getPools", store.state.meet.meet.meetingId);
			} catch (error) {
				console.error(error);
			}
		},

		selectVotation(id) {
			this.votations.forEach(async (element) => {
				if (element.formId === id) {
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
	width: 100% !important;
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

		@media screen and (max-width: 650px) {
			flex-direction: column;
		}

		.questions {
			width: 30%;
			height: 100%;
			border-right: 1px solid #cecece;
			overflow: scroll;
			max-height: 500px;
			@media screen and (max-width: 650px) {
				width: 100%;
				max-height: 45%;
			}

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
					max-height: 80px;
					overflow: hidden;
					text-overflow: ellipsis;
				}
			}
		}

		.results {
			width: 70%;
			height: 100%;
			padding: 20px;
			overflow: scroll;
			@media (max-width: 650px) {
				width: 100%;
				max-height: 55%;
				border-top: 1px solid #cecece;
			}

			h1 {
				font-weight: bold;
				font-size: 1.2rem;
			}

			.question {
				max-height: 300px;
				overflow: scroll;
				@media (max-width: 650px) {
					width: 100%;
				}

				.question-votation {
					margin: 20px;
					list-style: none;

					.question-text {
						font-weight: bold;
						font-size: 1rem;
						max-height: 50px;
						overflow: hidden;
						text-overflow: ellipsis;
					}

					ul {
						display: flex;
						flex-direction: row;
						flex-wrap: wrap;
						width: 100%;
						height: 100%;

						li {
							display: flex;
							align-items: center;
							flex-direction: column;
							min-width: 150px;
							max-width: 150px;
							max-height: 100px;
							border: 1px solid #cecece;
							padding: 10px;
							overflow-x: scroll;
							margin: 10px;

							@media (max-width: 650px) {
								width: 100%;
							}

							p {
								height: 70%;
								overflow: hidden;
								text-overflow: ellipsi;
							}
						}
					}
				}
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
	.btn-position-false {
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
		margin-top: 20px;

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
				width: 100%;
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
						align-items: flex-start;
						margin: 10px;
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
		button {
			width: auto;
			&[type="submit"] {
				max-width: 250px;
				margin-bottom: 50px;
			}
		}
	}
}
</style>
