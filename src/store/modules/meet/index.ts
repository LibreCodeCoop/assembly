import { Module } from "vuex";
import { RootState } from "@/store";
import { actions } from "./actions";
import { mutations } from "./mutations";
import { MeetState, initialState } from "./state";

export * from "./state";

export const store: Module<MeetState, RootState> = {
	state: initialState,
	mutations,
	actions,
};
