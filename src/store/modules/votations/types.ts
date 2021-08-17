import { IVotation } from "@/entities/Votations";

export enum Types {
	ADD_VOTATION = "votations/add_votation",
	GET_VOTATION = "votations/get_votation",
	ADD_VOTATIONS = "votations/add_votations",
	GET_VOTATIONS = "votations/get_votations",
}

export class AddVotation implements FluxStandardAction {
	type = Types.ADD_VOTATION;
	constructor(public payload: IVotation) {}
}
export class GetVotation implements FluxStandardAction {
	type = Types.GET_VOTATION;
}
export class AddVotations implements FluxStandardAction {
	type = Types.ADD_VOTATIONS;
	constructor(public payload: IVotation[]) {}
}
export class GetVotations implements FluxStandardAction {
	type = Types.GET_VOTATIONS;
}
