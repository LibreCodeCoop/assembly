declare module "*.vue" {
	import Vue from "vue";
	export default Vue;
}
declare module "@nextcloud/vue" {
	declare let AppContent: any;
	export default AppContent;
}
