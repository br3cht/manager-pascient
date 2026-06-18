import authService from '../../services/auth.service';

export default {
    namespaced: true,

    state: () => ({
        token: localStorage.getItem('auth_token'),
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => Boolean(state.token),
    },

    mutations: {
        SET_TOKEN(state, token) {
            state.token = token;
        },
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        SET_ERROR(state, error) {
            state.error = error;
        },
    },

    actions: {
        async login({ commit }, credentials) {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            try {
                const { data } = await authService.login(credentials);
                commit('SET_TOKEN', data.token);
                localStorage.setItem('auth_token', data.token);
            } catch (error) {
                commit('SET_ERROR', error.response?.data?.message || 'Nao foi possivel autenticar.');
                throw error;
            } finally {
                commit('SET_LOADING', false);
            }
        },

        async logout({ commit, state }) {
            try {
                if (state.token) {
                    await authService.logout();
                }
            } finally {
                commit('SET_TOKEN', null);
                localStorage.removeItem('auth_token');
            }
        },
    },
};
