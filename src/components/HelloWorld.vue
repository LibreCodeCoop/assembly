<template>
	<div class="tst">
		<h1>HelloWorld</h1>
	</div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from "vue-property-decorator";

@Component
export default class HelloWorld extends Vue {
	@Prop() private msg!: string;
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
.tst {
	width: 100%;

	h1 {
		font-size: 150px;
	}
}
</style>
