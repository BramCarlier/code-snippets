import Vue from 'vue';
import VueX from 'vuex';

import apps from './modules/apps';
import customers from './modules/customers';
import navigation from './modules/navigation';
import users from './modules/users';

Vue.use(VueX);

export default new VueX.Store({
    modules: {
        customers,
        users,
        navigation,
        apps,
    },
    state: {
        showModal: false,
    },
});
