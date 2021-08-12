<template>
	<div class="container">
		<div class="content">
			<div class="room" v-for="day in calendar" :key="day.day">
				<div class="date">
					<span>{{ day.dDay }}</span>
					<h3>{{ day.day }}</h3>
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
						<div class="author">
							<span>Por {{ room.author }}</span>
						</div>
						<div class="n-call">
							<span> {{ room.call }}</span>
						</div>
						<div class="hour">
							<span>{{ room.hour }}</span>
						</div>

						<button
							@click.prevent="console.log('ola')"
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

export default Vue.extend({
	name: "Room",
	data: () => ({
		url: "",
		calendar: [
			{
				day: 13,
				dDay: "Terça",
				rooms: [
					{
						id: 1,
						description: "Assembleia Geral orginaria",
						author: "Vinicios Gomes",
						call: "1a Chamada",
						hour: "09:10",
					},
					{
						id: 2,
						description: "Assembleia Geral orginaria",
						author: "Vinicios Gomes",
						call: "1a Chamada",
						hour: "09:10",
					},
				],
			},
			{
				day: 13,
				dDay: "Terça",
				rooms: [
					{
						id: 1,
						description: "Assembleia Geral orginaria",
						author: "Vinicios Gomes",
						call: "1a Chamada",
						hour: "09:10",
					},
					{
						id: 2,
						description: "Assembleia Geral orginaria",
						author: "Vinicios Gomes",
						call: "1a Chamada",
						hour: "09:10",
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

	.content {
		display: flex;
		flex-direction: column;

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

				h1 {
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

					div {
						margin: 0 30px;
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
