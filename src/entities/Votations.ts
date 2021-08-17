export interface IQuestion {
	text: string;
	total: number;
}

export interface IVotations {
	title: string;
	available: number;
	description: string;
	id: number;
	status: string;
	finished_at: string;
	voted: boolean;
	responses: IQuestion[];
}
