import { ActionContext } from "vuex";
import axios from "@nextcloud/axios";
import { RootState } from "@/store";
import { generateUrl } from "@nextcloud/router";
import { IVotation } from "@/entities/Votations";
import { AddVotations, ToggleModal } from "../votations/types";
import { VotationsState } from "./state";

export const actions = {
	// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
	async getPools(
		{ commit, dispatch }: ActionContext<VotationsState, RootState>,
		meetingId: string
	) {
		const response = await axios.get(
			generateUrl(`/apps/assembly/api/v1/pools/${meetingId}`)
		);
		const pool: IVotation[] = response.data;
		pool.some((elem) => {
			if (elem.status === "enabled" && elem.voted === false) {
				dispatch("getForms", elem.formId);
				commit(new ToggleModal(true));
			}
		});
		commit(new AddVotations(pool));
	},
};
