import { Module } from "vuex";
import { RootState } from "@/store";
import { mutations } from "./mutations";
import { VotationsState, initialState } from "./state";

export * from "./state";

export const store: Module<VotationsState, RootState> = {
	state: initialState,
	mutations,
};
