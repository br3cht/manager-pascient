export default {
    namespaced: true,

    state: () => ({
        open: false,
        message: '',
        color: 'success',
        timeout: 4000,
    }),

    mutations: {
        SHOW(state, { message, color, timeout }) {
            state.message = message;
            state.color = color;
            state.timeout = timeout;
            state.open = true;
        },
        CLOSE(state) {
            state.open = false;
        },
    },

    actions: {
        notify({ commit }, { message, color = 'success', timeout = 4000 }) {
            commit('SHOW', { message, color, timeout });
        },

        success({ dispatch }, message) {
            dispatch('notify', { message, color: 'success' });
        },

        error({ dispatch }, message) {
            dispatch('notify', { message, color: 'error', timeout: 6000 });
        },

        close({ commit }) {
            commit('CLOSE');
        },
    },
};
