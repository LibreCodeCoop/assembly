import { Module } from "vuex";
import { RootState } from "@/store";
import { mutations } from "./mutations";
import { actions } from "./actions";
import { FormState, initialState } from "./state";

export * from "./state";

export const store: Module<FormState, RootState> = {
	state: initialState,
	mutations,
	actions,
};
