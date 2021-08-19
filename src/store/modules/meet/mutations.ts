import { MutationTree } from "vuex";
import { MeetState } from "./state";
import { AddMeet, AddMeets, Types } from "./types";

export const mutations: MutationTree<MeetState> = {
	[Types.ADD_MEET]: (state, action: AddMeet) => {
		const meet = action.payload;
		state.meet = meet;
	},
	[Types.GET_MEET]: (state) => {
		return state.meet;
	},
	[Types.ADD_MEETS]: (state, action: AddMeets) => {
		const meets = action.payload;
		state.meets = meets;
	},
	[Types.GET_MEETS]: (state) => {
		return state.meets;
	},
};
