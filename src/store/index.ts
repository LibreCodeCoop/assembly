import Vue from "vue";
import Vuex from "vuex";

import * as meet from "@/store/modules/meet";
import * as votations from "@/store/modules/votations";
import * as form from "@/store/modules/form";

Vue.use(Vuex);

export interface RootState {
	meet: meet.MeetState;
	votations: votations.VotationsState;
	form: form.FormState;
}

export default new Vuex.Store({
	modules: {
		meet: meet.store,
		votations: votations.store,
		form: form.store,
	},
});
