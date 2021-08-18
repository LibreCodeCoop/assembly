import { IForm } from "@/entities/Form";

export enum Types {
	ADD_FORM = "form/add_form",
	GET_FORM = "form/get_form",
	ADD_FORMS = "form/add_forms",
	GET_FORMS = "form/get_forms",
}

export class AddForm implements FluxStandardAction {
	type = Types.ADD_FORM;
	constructor(public payload: IForm) {}
}

export class GetForm implements FluxStandardAction {
	type = Types.GET_FORM;
}

export class AddForms implements FluxStandardAction {
	type = Types.ADD_FORMS;
	constructor(public payload: IForm[]) {}
}

export class GetMeets implements FluxStandardAction {
	type = Types.GET_FORMS;
}
