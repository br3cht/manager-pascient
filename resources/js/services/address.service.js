import api from './api';

export default {
    list(params) {
        return api.get('/addresses', { params });
    },

    get(id) {
        return api.get(`/addresses/${id}`);
    },

    create(payload) {
        return api.post('/addresses', payload);
    },

    update(id, payload) {
        return api.put(`/addresses/${id}`, payload);
    },

    delete(id) {
        return api.delete(`/addresses/${id}`);
    },
};
