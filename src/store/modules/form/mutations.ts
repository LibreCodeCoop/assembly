import { MutationTree } from "vuex";
import { FormState } from "./state";
import { AddForm, AddForms, Types } from "./types";

export const mutations: MutationTree<FormState> = {
	[Types.ADD_FORM]: (state, action: AddForm) => {
		const form = action.payload;
		state.form = form;
	},
	[Types.GET_FORM]: (state) => {
		return state.form;
	},
	[Types.ADD_FORMS]: (state, action: AddForms) => {
		const forms = action.payload;
		state.forms = forms;
	},
	[Types.GET_FORMS]: (state) => {
		return state.forms;
	},
};
