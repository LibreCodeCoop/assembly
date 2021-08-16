import { IMeet } from "@/entities/Meet";

export interface MeetState {
	meet: IMeet | null;
	meets: List<IMeet> | null;
}

export const initialState = (): MeetState => {
	return {
		meets: null,
		meet: null,
	};
};
