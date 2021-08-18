import { IForm } from "@/entities/Form";

export interface FormState {
	form: IForm | null;
	forms: IForm[] | null;
}

export const initialState = (): FormState => {
	return {
		form: null,
		forms: null,
	};
};
