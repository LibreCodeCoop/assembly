<!--
- @copyright Copyright (c) 2021 Lyseon Tech <contato@lt.coop.br>
-
- @author Lyseon Tech <contato@lt.coop.br>
- @author Vinicios Gomes <viniciusgomesvaian@gmail.com>
-
- @license GNU AGPL version 3 or any later version
-
- This program is free software: you can redistribute it and/or modify
- it under the terms of the GNU Affero General Public License as
- published by the Free Software Foundation, either version 3 of the
- License, or (at your option) any later version.
-
- This program is distributed in the hope that it will be useful,
- but WITHOUT ANY WARRANTY; without even the implied warranty of
- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
- GNU Affero General Public License for more details.
-
- You should have received a copy of the GNU Affero General Public License
- along with this program.  If not, see <http://www.gnu.org/licenses/>.
-
-->

<template>
	<Content class="content-app" app-name="assembly">
		<!-- <AppNavigation> -->
		<NavBar>
			<Menu />
		</NavBar>
		<!-- </AppNavigation> -->
		<AppContent id="container">
			<router-view />
		</AppContent>
	</Content>
</template>

<script>
import Content from '@nextcloud/vue/dist/Components/Content'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import NavBar from './Components/NavBar'
import Menu from './Components/Menu'
export default {
	name: 'App',
	components: {
		NavBar,
		Menu,
		AppContent,
		Content,
	},
}
</script>
<style scoped>
.content-app{
	background-color: #0082c9;
	background-image: linear-gradient(40deg, #0082c9 0%, #30b6ff 100%);
	height: 100%;
}

#container{
	background-color: #FFF;
	border-radius: 50px 0 0 50px;
	padding: 20px 0 0 20px;
}
</style>
