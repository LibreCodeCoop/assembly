export interface IQuestion {
	text: string;
	total: number;
}

export interface IVotation {
	title: string;
	available: number;
	description: string;
	id: number;
	status: string;
	finished_at: string;
	voted: boolean;
	responses: IQuestion[];
}
