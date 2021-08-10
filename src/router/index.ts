import Vue from "vue";
import { generateUrl } from "@nextcloud/router";
import VueRouter from "vue-router";
import { routes } from "./routes";
const router = new VueRouter({
	mode: "history",
	base: generateUrl("/apps/assembly/"),
	routes,
});

export default router;
