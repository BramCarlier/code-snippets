export default {
    namespaced: true,
    state: {
        namespace: 'navigation',
        pages: {},
    },
    actions: {
        loadPages({commit}, pages) {
            commit('UPDATE_PAGES', pages);
        },
        toggleActiveState({commit}, toggledPage) {
            commit('UPDATE_ACTIVE_STATE', toggledPage);
        }
    },
    mutations: {
        UPDATE_PAGES(state, pages) {
            state.pages = pages;
        },
        UPDATE_ACTIVE_STATE(state, toggledPage) {
            if (typeof toggledPage === 'string') {
                toggledPage = toggledPage.split('.');
            }

            for (let page in state.pages) {
                if (state.pages[page] !== state.pages[toggledPage[0]]) {
                    state.pages[page].active = false;
                }
            }

            if (state.pages[toggledPage[0]]) {
                state.pages[toggledPage[0]].active = !state.pages[toggledPage[0]].active;

                if (state.pages[toggledPage[0]].children && toggledPage[1]) {
                    state.pages[toggledPage[0]].children[toggledPage[1]].active =
                        !state.pages[toggledPage[0]].children[toggledPage[1]].active;
                }
            }
        }
    },
};
