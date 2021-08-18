export interface IQuestion {
	text: string;
	total: number;
}

export interface IVotation {
	title: string;
	available: number;
	description: string;
	formId: number;
	status: string;
	finishedAt: string;
	voted: boolean;
	responses: IQuestion[];
}
