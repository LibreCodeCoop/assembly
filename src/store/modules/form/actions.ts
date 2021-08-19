import { ActionContext } from "vuex";
import axios from "@nextcloud/axios";
import { RootState } from "@/store";
import { FormState } from "./state";
import { generateOcsUrl } from "@nextcloud/router";
import { AddForm } from "./types";
import { IForm } from "@/entities/Form";

export const actions = {
	// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
	async getForms(
		{ commit }: ActionContext<FormState, RootState>,
		formId: number
	) {
		const response = await axios.get(
			generateOcsUrl(`/apps/forms/api/v1/form/${formId}`)
		);
		const form: IForm = response.data.ocs.data;
		commit(new AddForm(form));
	},
};
