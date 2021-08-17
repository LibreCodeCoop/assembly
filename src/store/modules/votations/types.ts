import { IVotations } from "@/entities/Votations";

export enum Types {
	ADD_VOTATIONS = "votations/add_votations",
	GET_VOTATIONS = "votations/get_votations",
}

export class AddVotations implements FluxStandardAction {
	type = Types.ADD_VOTATIONS;
	constructor(public payload: IVotations[]) {}
}
export class GetVotations implements FluxStandardAction {
	type = Types.GET_VOTATIONS;
}
