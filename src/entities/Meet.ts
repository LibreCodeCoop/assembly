interface IUserOwner {
	display_name: string;
	user_id: string;
}

export enum status {
	waiting,
	done,
	cancelled,
	inProgress,
}

export interface IMeet {
	id: number;
	date: string;
	createdAt: string;
	createdBy: IUserOwner;
	description: string;
	deletedAt?: number;
	meetingId: string;
	slug?: string;
	url: string;
	meetUrl: string;
	status: string;
}
