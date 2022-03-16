export default {
    namespaced: true,
    state: {
        loading: false,
        user: {
            selected_projects: [],
        }
    },
    actions: {
        loadUser({commit}) {
            //do axios call to web endpoint using api
            commit('SET_USER', {
                selected_projects: [],
            });
        },

        updateLanguage({commit}) {
            //do axios call to web endpoint using api
            commit('SET_USER', {
                selected_projects: [],
            });
        },

        updateProjects({commit}) {
            //do axios call to web endpoint using api
            commit('SET_USER', {
                selected_projects: [],
            });
        }
    },
    mutations: {
        SET_USER(state, user) {
            state.user = user;
        },

        SET_PROJECTS(state, customers) {
            state.user.selected_projects = customers;
        },

        ADD_PROJECT(state, customer) {
            state.user.selected_projects.push(customer);
        },

        SET_LOADING(state, loading = true) {
            state.loading = loading;
        }
    },

    getters: {
        hasCustomersSelected: state => state.user.selected_projects ? state.user.selected_projects.length > 0 : false,
        hasOneCustomerSelected: state => state.user.selected_projects
            ? state.user.selected_projects.length === 1
            : false,
        hasMoreThanOneCustomerSelected: state => state.user.selected_projects
            ? state.user.selected_projects.length > 1
            : false,
        getSelectedCustomerId: (state, getters) => getters.hasOneCustomerSelected
            ? state.user.selected_projects[0]
            : null,
        getAllSelectedCustomerIds: (state, getters) => getters.hasCustomersSelected
            ? state.user.selected_projects
            : null,
    }
};
