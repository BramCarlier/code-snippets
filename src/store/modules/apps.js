export default {
    namespaced: true,
    state: {
        namespace: 'apps',
        apps: [],
    },
    actions: {
        getApps({commit}) {
            //do axios call to web endpoint using api
            commit('UPDATE_APPS', []);
        },
    },
    mutations: {
        UPDATE_APPS(state, apps) {
            state.apps = apps;
        }
    },
};
