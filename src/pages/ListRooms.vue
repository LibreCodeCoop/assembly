<template>
	<div class="container">
		<div class="content">
			<div class="room" v-for="date in filterByDate" :key="date.day">
				<div class="date">
					<span>{{ returnDayOfWeek(date.day) }}</span>
					<h3>{{ returnDay(date.day) }}</h3>
					<!-- <span v-if="day.today">Hoje</span> -->
				</div>
				<div class="rooms-peer-date">
					<table>
						<tbody>
							<tr v-for="meet in date.meets" :key="meet.id">
								<td class="subject">
									<div>
										<h1>{{ meet.description }}</h1>
									</div>
								</td>
								<td class="author">
									<div>
										<p class="icon-user"></p>
										<span>
											{{
												t("assembly", "By {name}", {
													name: meet.created_by
														.display_name
														? meet.created_by
																.display_name
														: meet.created_by
																.user_id,
												})
											}}
										</span>
									</div>
								</td>
								<td :class="'author ' + meet.status">
									<div>
										<p class="icon-category-monitoring" />
										<span>
											{{
												normalizeStatus(meet.status)
											}}</span
										>
									</div>
								</td>
								<td class="hour">
									<div>
										<p class="icon-calendar-dark" />
										<span>{{ formatDate(meet.date) }}</span>
									</div>
								</td>
								<td>
									<button
										class="primary"
										@click="redirect(meet)"
									>
										Acessar
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<EmptyContent v-show="filterByDate === false">
				<template #desc>
					{{ t("assembly", "You don't have Meets") }}
				</template>
			</EmptyContent>
		</div>
	</div>
</template>
<script lang="ts">
import Vue from "vue";
import { format } from "date-fns";
import { EmptyContent } from "@nextcloud/vue";
import { ptBR } from "date-fns/locale";
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import store from "@/store";
import { AddMeet, AddMeets } from "@/store/modules/meet/types";
import { IMeet } from "@/entities/Meet";

export default Vue.extend({
	name: "Room",

	components: {
		EmptyContent,
	},

	data: () => ({
		url: "",
	}),
	created() {
		this.getData();
	},
	computed: {
		meets() {
			return store.state.meet.meets;
		},
		filterByDate() {
			if (this.meets) {
				const allDays = this.meets
					.map((item) => {
						const date = item.date.split(" ")[0];
						return date;
					})
					.filter((item, index, self) => {
						return self.indexOf(item) == index;
					})
					.map((arr) => {
						return { day: arr, meets: [] };
					});
				return allDays.map((day) => {
					const meets = this.meets.filter((meet) => {
						return meet.date.split(" ")[0] == day.day;
					});
					day.meets = meets;
					return day;
				});
			}
			return false;
		},
	},
	methods: {
		returnDayOfWeek(date) {
			return format(new Date(date), "EEE", { locale: ptBR });
		},

		returnDay(date) {
			return format(new Date(date), "dd");
		},

		async getData() {
			try {
				const response = await axios.get(
					generateUrl("/apps/assembly/api/v1/meet")
				);
				await store.commit(new AddMeets(response.data));
			} catch (err) {
				console.error(err.response);
			}
		},

		normalizeStatus(str) {
			return str.replace("_", " ");
		},

		async redirect(meet: IMeet) {
			await store.commit(new AddMeet(meet));
			this.$router.push({ name: "meet" });
		},

		formatDate(date) {
			return format(new Date(date), "dd/MM/yyyy HH:mm");
		},
	},
});
</script>

<style lang="scss" scoped>
.container {
	display: flex;
	height: 100%;

	.content {
		display: flex;
		flex-direction: column;
		font-size: 1rem;
		width: 100%;

		.room {
			display: flex;
			flex-direction: row;
			padding: 10px;
			margin: 0 20px;
			width: 100%;
			border-bottom: 1px solid #cecece;

			.date {
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
				margin: 10px;
				padding: 10px;

				h3 {
					font-size: 2.4rem;
					font-weight: normal;
					text-align: center;
					margin-top: 15px;
				}
				@media screen and(max-width:750px ) {
					display: none;
				}
			}

			.rooms-peer-date {
				height: 100%;
				width: 100%;
				display: flex;
				justify-items: center;
				align-items: center;

				table {
					width: 100%;
					tr {
						td {
							div {
								display: flex;
								flex-direction: row;

								p {
									margin-right: 10px;
								}
							}
						}
						@media screen and(max-width: 750px ) {
							display: flex;
							flex-direction: column;
							border-left: 2px solid #4a4683;
							margin-bottom: 30px;

							td {
								margin-left: 10px;
							}

							.subject {
								h1 {
									border: none;
								}
							}
						}
					}
					.subject {
						h1 {
							padding: 6px 24px 6px 10px;
							border-left: 2px solid #4a4683;
							background-color: #f6faff;
						}
					}

					.n-call {
						span {
							&::first-letter {
								text-transform: uppercase;
							}
						}
					}
					.waiting {
						color: #ffc107 !important;
					}
					.done {
						color: #28a745 !important;
					}
					.cancelled {
						color: #dc3545 !important;
					}
					.in_progress {
						color: #007bff !important;
					}
				}
			}

			@media (max-width: 750px) {
				border-bottom: none;
			}
		}
	}
}
</style>
