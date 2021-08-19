import { IMeet } from "@/entities/Meet";

export enum Types {
	ADD_MEET = "meet/add_meet",
	GET_MEET = "meet/get_meet",
	ADD_MEETS = "meet/add_meets",
	GET_MEETS = "meet/get_meets",
}

export class AddMeet implements FluxStandardAction {
	type = Types.ADD_MEET;
	constructor(public payload: IMeet) {}
}

export class GetMeet implements FluxStandardAction {
	type = Types.GET_MEET;
}

export class AddMeets implements FluxStandardAction {
	type = Types.ADD_MEETS;
	constructor(public payload: IMeet[]) {}
}

export class GetMeets implements FluxStandardAction {
	type = Types.GET_MEETS;
}
