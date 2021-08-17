import Vue from "vue";
import Vuex from "vuex";

import * as meet from "@/store/modules/meet";
import * as votations from "@/store/modules/votations";

Vue.use(Vuex);

export interface RootState {
	meet: meet.MeetState;
	votations: votations.VotationsState;
}

export default new Vuex.Store({
	modules: {
		meet: meet.store,
		votations: votations.store,
	},
});
