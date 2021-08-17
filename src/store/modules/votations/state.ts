import { IVotations } from "@/entities/Votations";

export interface VotationsState {
	votations: IVotations[] | null;
}

export const initialState = (): VotationsState => {
	return {
		votations: null,
	};
};
