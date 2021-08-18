interface IOptions {
	id: number;
	questionId: number;
	text: string;
}

interface IQuestion {
	formId: number;
	id: number;
	options: IOptions[];
}

export interface IForm {
	questions: IQuestion[];
	description: string;
	hash: string;
	ownerId: string;
	title: string;
}
