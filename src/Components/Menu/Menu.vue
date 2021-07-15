<template>
	<ul>
		<li>
			<Dashboard fill-color="#fff" />
			<p>Dashboard</p>
		</li>
		<li>
			< fill-color="#fff">
			<p>Meet</p>
		</li>
		<li>
			<CheckList fill-color="#fff" />
			<p>Votações</p>
		</li>
		<li>
			<ChartAreaspline fill-color="#fff" />
			<p>Resultados</p>
		</li>
	</ul>
</template>

<script>
import Dashboard from 'vue-material-design-icons/MonitorDashboard'
import ChartAreaspline from 'vue-material-design-icons/ChartAreaspline'
import CheckList from 'vue-material-design-icons/FormatListChecks'

export default {
	name: 'Menu',
	components: {
		Dashboard,
		ChartAreaspline,
		CheckList,
	},
}
</script>
<style scoped>
ul{
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 90%;
}

li{
	margin: 20px 0;
}
</style>
