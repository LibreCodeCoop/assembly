import { ActionContext } from "vuex";
import axios from "@nextcloud/axios";
import { MeetState } from "./state";
import { RootState } from "@/store";
import { generateUrl } from "@nextcloud/router";
import { IMeet } from "@/entities/Meet";
import { AddMeets } from "./types";
export const actions = {
	// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
	async getMeetings({ commit }: ActionContext<MeetState, RootState>) {
		try {
			const response = await axios.get(
				generateUrl("/apps/assembly/api/v1/meet")
			);
			const meets: IMeet[] = response.data;
			await commit(new AddMeets(meets));
		} catch (err) {
			console.error(err);
		}
	},
};
