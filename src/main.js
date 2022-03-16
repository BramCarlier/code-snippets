import Vue from 'vue'
import App from './App.vue'
import store from './store';

import SideNavigation from "@/components/SideNavigation";
import Banner from "@/components/banner/Banner";
import Customer from "@/components/banner/Customer";
import User from "@/components/banner/User";

Vue.config.productionTip = false;

function defaultOptions() {
    return {
        Pages: {
            '404': {
                name: 'No pages found'
            }
        },
        Links: {
            'my_account': {
                name: 'my_account',
                path: `#`,
            },
            'language': {
                name: 'language',
                languages: {
                    nl: 'Nederlands',
                    en: 'English',
                    fr: 'Français',
                    fr_CA: 'Français (CA)',
                    it: 'Italiano',
                },
            },
            'users': {
                name: 'users',
                path: `#`,
            },
            'file_explorer': {
                name: 'file_explorer',
                path: `#`,
            },
        }
    };
}


Vue.component('side-navigation', SideNavigation);
Vue.component('banner', Banner);
Vue.component('customer', Customer);
Vue.component('user', User);

Vue.prototype.vueTemplateStore = store;

Vue.prototype.$userOptions = {...defaultOptions()};


new Vue({
    render: h => h(App),
}).$mount('#app');
