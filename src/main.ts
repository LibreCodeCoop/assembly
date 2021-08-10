import Vue from "vue";
import { generateFilePath } from "@nextcloud/router";
import { getRequestToken } from "@nextcloud/auth";
import { translate as t, translatePlural as n } from "@nextcloud/l10n";

import App from "./App.vue";
import router from "./router";
import store from "./store";

Vue.config.productionTip = false;
Vue.prototype.t = t;
Vue.prototype.n = n;
Vue.mixin({ methods: { t, n } });

__webpack_nonce__ = btoa(getRequestToken() as string);

__webpack_public_path__ = generateFilePath("libresign", "", "js/");

new Vue({
	router,
	store,
	render: (h) => h(App),
}).$mount("#content");
