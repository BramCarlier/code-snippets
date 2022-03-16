export default {
    namespaced: true,
    state: {
        customers: [],
    },
    actions: {
        fetchCustomers({commit}) {
            //do axios call to web endpoint using api
            commit('SET_CUSTOMERS', []);
        },
    },
    mutations: {
        SET_CUSTOMERS(state, customers) {
            state.customers = customers;
        },
    },
};
