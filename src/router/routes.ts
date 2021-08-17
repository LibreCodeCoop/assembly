import { RouteConfig } from "vue-router";

export const routes: Array<RouteConfig> = [
	{
		path: "#list-rooms",
		component: () => import("@/pages/ListRooms.vue"),
		name: "room",
	},
	{
		path: "#meet",
		component: () => import("@/pages/Meet.vue"),
		name: "meet",
	},
];
