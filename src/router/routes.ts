import { RouteConfig } from "vue-router";

export const routes: Array<RouteConfig> = [
	{
		path: "/",
		component: () => import("@/views/Home.vue"),
		name: "Home",
	},
	{
		path: "#room",
		component: () => import("@/views/Room.vue"),
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
];
