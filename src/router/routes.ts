import { RouteConfig } from "vue-router";

export const routes: Array<RouteConfig> = [
	{
		path: "#list-rooms",
		component: () => import("@/pages/ListRooms.vue"),
		name: "room",
	},
	{
		path: "#votation",
		component: () => import("@/pages/Votation.vue"),
		name: "votation",
	},
	{
		path: "#results",
		component: () => import("@/pages/Results.vue"),
		name: "results",
	},
	{
		path: "#meet",
		component: () => import("@/pages/Meet.vue"),
		name: "meet",
	},
];
