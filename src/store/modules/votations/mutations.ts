import { MutationTree } from "vuex";
import { VotationsState } from "./state";
import { AddVotation, AddVotations, Types } from "./types";

export const mutations: MutationTree<VotationsState> = {
	[Types.ADD_VOTATION]: (state, action: AddVotation) => {
		const votation = action.payload;
		state.votation = votation;
	},
	[Types.GET_VOTATION]: (state) => {
		return state.votation;
	},
	[Types.ADD_VOTATIONS]: (state, action: AddVotations) => {
		const votations = action.payload;
		state.votations = votations;
	},
	[Types.GET_VOTATIONS]: (state) => {
		return state.votations;
	},
};
