import { IVotation } from "@/entities/Votations";

export interface VotationsState {
	votation: IVotation | null;
	votations: IVotation[] | null;
	isEnabledModal: boolean;
}

export const initialState = (): VotationsState => {
	return {
		votations: null,
		isEnabledModal: false,
		votation: {
			available: 0,
			description: "",
			finishedAt: "",
			formId: 0,
			responses: [],
			status: "",
			title: "",
			voted: false,
		},
	};
};
