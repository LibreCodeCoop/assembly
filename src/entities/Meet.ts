interface IUserOwner {
	display_name: string;
	user_id: string;
}

export enum status {
	waiting,
	done,
	cancelled,
	in_progress,
}

export interface IMeet {
	id: number;
	date: string;
	created_at: string;
	created_by: IUserOwner;
	description: string;
	meetUrl: string;
	status: string;
}
