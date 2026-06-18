import dashboardService from '../../services/dashboard.service';

export default {
    namespaced: true,

    state: () => ({
        totals: {
            patients_total: 0,
            addresses_total: 0,
        },
        loading: false,
    }),

    mutations: {
        SET_TOTALS(state, totals) {
            state.totals = totals;
        },
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
    },

    actions: {
        async fetchTotals({ commit, state }) {
            commit('SET_LOADING', true);

            try {
                const { data } = await dashboardService.totals();
                commit('SET_TOTALS', data.data || state.totals);
            } finally {
                commit('SET_LOADING', false);
            }
        },
    },
};
