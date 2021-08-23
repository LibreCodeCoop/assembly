import Vue from "vue";
import { translate as t, translatePlural as n } from "@nextcloud/l10n";

import App from "./App.vue";

Vue.config.productionTip = false;
Vue.prototype.t = t;
Vue.prototype.n = n;
Vue.mixin({ methods: { t, n } });

new Vue({
	render: (h) => h(App),
}).$mount("#assembly-admin-settings");
