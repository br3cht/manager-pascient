import patientService from '../../services/patient.service';

export default {
    namespaced: true,

    state: () => ({
        items: [],
        meta: {},
        current: null,
        loading: false,
    }),

    mutations: {
        SET_ITEMS(state, items) {
            state.items = items;
        },
        SET_META(state, meta) {
            state.meta = meta;
        },
        SET_CURRENT(state, current) {
            state.current = current;
        },
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
    },

    actions: {
        async fetch({ commit }, params) {
            commit('SET_LOADING', true);

            try {
                const { data } = await patientService.list(params);
                commit('SET_ITEMS', data.data || []);
                commit('SET_META', data.meta || {});
            } finally {
                commit('SET_LOADING', false);
            }
        },

        async fetchOne({ commit }, id) {
            commit('SET_LOADING', true);

            try {
                const { data } = await patientService.get(id);
                commit('SET_CURRENT', data.data);
                return data.data;
            } finally {
                commit('SET_LOADING', false);
            }
        },

        async save(_, { payload, id }) {
            const request = id ? patientService.update(id, payload) : patientService.create(payload);
            const { data } = await request;
            return data;
        },

        async delete(_, id) {
            const { data } = await patientService.delete(id);
            return data;
        },
    },
};
