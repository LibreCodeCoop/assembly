import Vue from "vue";
import VueRouter from "vue-router";

import { generateUrl } from "@nextcloud/router";
import { routes } from "./routes";

Vue.use(VueRouter);

const router = new VueRouter({
	mode: "history",
	base: generateUrl("/apps/assembly/"),
	routes,
});

export default router;
