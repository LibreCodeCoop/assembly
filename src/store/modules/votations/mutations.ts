import { MutationTree } from "vuex";
import { VotationsState } from "./state";
import { AddVotations, Types } from "./types";

export const mutations: MutationTree<VotationsState> = {
	[Types.ADD_VOTATIONS]: (state, action: AddVotations) => {
		const votations = action.payload;
		state.votations = votations;
	},
	[Types.GET_VOTATIONS]: (state) => {
		return state.votations;
	},
};
