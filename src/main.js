/* eslint-disable vue/match-component-file-name */
/**
 * @copyright Copyright (c) 2020 Lyseon Tech <contato@lt.coop.br>
 *
 * @author Lyseon Tech <contato@lt.coop.br>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

import { getRequestToken } from '@nextcloud/auth'
import { generateFilePath } from '@nextcloud/router'
import { sync } from 'vuex-router-sync'
import Vue from 'vue'

import App from './App'
import router from './router'
import store from './store'

/** GLOBAL COMPONENTS AND DIRECTIVE */
import VTooltip from '@nextcloud/vue/dist/Directives/Tooltip'

// Dialogs css
import '@nextcloud/dialogs/styles/toast.scss'

// CSP config for webpack dynamic chunk loading
// eslint-disable-next-line
__webpack_nonce__ = btoa(getRequestToken())

// Correct the root of the app for chunk loading
// OC.linkTo matches the apps folders
// OC.generateUrl ensure the index.php (or not)
// We do not want the index.php since we're loading files
// eslint-disable-next-line
__webpack_public_path__ = generateFilePath('contacts', '', 'js/')

// Register global directives
Vue.directive('Tooltip', VTooltip)

sync(store, router)

Vue.mixin({ methods: { t, n } })

export default new Vue({
	el: '#content',
	router,
	store,
	render: h => h(App),
})
