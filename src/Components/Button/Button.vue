<template>
	<form>
		<h1>Isso é um form</h1>
		<Button>
			OK button
		</Button>
		<Paragrafo />
	</form>
</template>
<script>
import { Button, Paragrafo } from './styles'
export default {
	components: {
		Button,
		Paragrafo,
	},
}
</script>
