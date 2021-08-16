<template>
	<div class="container">
		<div class="content">
			<div class="room" v-for="day in calendar" :key="day.day">
				<div class="date">
					<span>{{ day.dDay }}</span>
					<h3>{{ day.day }}</h3>
					<span v-if="day.today">Hoje</span>
				</div>
				<div class="rooms-peer-date">
					<div
						v-for="room in day.rooms"
						:key="room.id"
						class="content-peer-date"
					>
						<div class="subject">
							<h1>{{ room.description }}</h1>
						</div>
						<div class="flex-row author">
							<p class="icon-user"></p>
							<span>
								Por
								{{
									room.created_by.display_name
										? room.created_by.display_name
										: room.created_by.user_id
								}}
							</span>
						</div>
						<div :class="'flex-row n-call ' + room.status">
							<p class="icon-category-monitoring" />
							<span> {{ normalizeStatus(room.status) }}</span>
						</div>
						<div class="flex-row hour">
							<p class="icon-calendar" />
							<span>{{ formatDate(room.date) }}</span>
						</div>

						<button class="primary" @click="redirect(url)">
							Acessar
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script lang="ts">
import Vue from "vue";
import { format } from "date-fns";
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import store from "@/store";
import { AddMeet, AddMeets } from "@/store/modules/meet/types";
import { IMeet, status } from "@/entities/Meet";

export default Vue.extend({
	name: "Room",

	data: () => ({
		url: "",
	}),
	created() {
		this.getData();
		this.fetchMockMeets();
	},
	methods: {
		async fetchMockMeets() {
			const meetList: IMeet[] = [
				{
					id: 1,
					date: "2021-08-16 16:09:01",
					created_at: "2021-08-15 15:02:23",
					created_by: {
						display_name: "nextcloud",
						user_id: "nextcloud User ID",
					},
					description: "Assembleia Geral orginaria",
					meetUrl: "getTi",
					status: "waiting",
				},
				{
					id: 2,
					date: "2021-08-16 16:09:01",
					created_at: "2021-08-15 15:02:23",
					created_by: {
						display_name: "nextcloud",
						user_id: "nextcloud User ID",
					},
					description: "Assembleia Geral orginaria",
					meetUrl: "getTi",
					status: "waiting",
				},
				{
					id: 3,
					date: "2021-08-17 16:09:01",
					created_at: "2021-08-17 15:02:23",
					created_by: {
						display_name: "nextcloud",
						user_id: "nextcloud User ID",
					},
					description: "Assembleia Geral orginaria",
					meetUrl: "getTi",
					status: "waiting",
				},
			];

			await store.commit(new AddMeets(meetList));
		},
		async getData() {
			try {
				const response = await axios.get(
					generateUrl("/apps/assembly/api/v1/dashboard")
				);
				this.url = response.data.meetUrl;
				await store.commit(new AddMeet(response.data.meetUrl));
			} catch (err) {
				console.error(err.response);
			}
		},
		normalizeStatus(str) {
			return str.replace("_", " ");
		},
		redirect() {
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
			}

			.rooms-peer-date {
				height: 100%;
				width: 100%;

				.content-peer-date {
					display: flex;
					flex-direction: row;
					justify-content: space-around;
					align-items: center;
					width: 100%;
					color: #5a5b61;
					margin: 15px 0;

					div {
						margin: 0 30px;
					}
					.flex-row {
						display: flex;
						flex-direction: row;
					}
					p {
						margin: 0 5px;
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
		}
	}
}
</style>
