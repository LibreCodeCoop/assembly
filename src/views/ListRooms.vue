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
							<span>Por {{ room.author }}</span>
						</div>
						<div class="flex-row n-call">
							<p class="icon-category-monitoring" />
							<span> {{ room.call }}</span>
						</div>
						<div class="flex-row hour">
							<p class="icon-calendar" />
							<span>{{ room.hour }}</span>
						</div>

						<button
							@click.prevent="acessRoom(room.url)"
							class="primary"
						>
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
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import AddNewMeetUseCase, { IAddNewMeetUseCase } from "@/usecases/AddNewMeet";
import { MeetEntity } from "@/entities/Meet";
import ErrorService from "@/srvices/ErrorService";

export default Vue.extend({
	name: "Room",

	data: () => ({
		url: "",
		calendar: [
			{
				day: 13,
				dDay: "Terça",
				today: true,
				rooms: [
					{
						id: 1,
						description: "Assembleia Geral orginaria",
						author: "Vinicios Gomes",
						call: "1a Chamada",
						hour: "09:10",
						url: "getTi",
					},
					{
						id: 2,
						description: "Assembleia Geral orginaria",
						author: "Vinicios Gomes",
						call: "1a Chamada",
						hour: "09:10",
						url: "getTi",
					},
				],
			},
			{
				day: 14,
				dDay: "Quarta",
				rooms: [
					{
						id: 1,
						description: "Assembleia Geral orginaria",
						author: "Vinicios Gomes",
						call: "1a Chamada",
						hour: "10:12",
						url: "getTi",
					},
					{
						id: 2,
						description: "Assembleia Geral orginaria",
						author: "Vinicios Gomes",
						call: "1a Chamada",
						hour: "09:10",
						url: "getTi",
					},
				],
			},
		],
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
			} catch (err) {
				console.error(err.response);
			}
		},
		async acessRoom(url) {
			const params: IAddNewMeetUseCase = {
				meet: url,
				errorService: new ErrorService({
					context: "Add new Meet",
				}),
			};
			await new AddNewMeetUseCase(params).execute(url);
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
				}
			}
		}
	}
}
</style>
