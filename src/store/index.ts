import Vue from "vue";
import Vuex from "vuex";

import * as meet from "@/store/modules/meet";

Vue.use(Vuex);

export interface RootState {
	meet: meet.MeetState;
}

export default new Vuex.Store({
	modules: {
		meet: meet.store,
	},
});
