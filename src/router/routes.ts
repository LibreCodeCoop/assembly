import { RouteConfig } from "vue-router";

export const routes: Array<RouteConfig> = [
	{
		path: "#list-rooms",
		component: () => import("@/views/ListRooms.vue"),
		name: "room",
	},
	{
		path: "#votation",
		component: () => import("@/views/Votation.vue"),
		name: "votation",
	},
	{
		path: "#results",
		component: () => import("@/views/Results.vue"),
		name: "results",
	},
	{
		path: "#room",
		component: () => import("@/components/Meet"),
		name: "room",
	},
];
