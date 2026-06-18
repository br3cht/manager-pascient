import api from './api';

export default {
    list(params) {
        return api.get('/patients', { params });
    },

    get(id) {
        return api.get(`/patients/${id}`);
    },

    create(payload) {
        return api.post('/patients', payload);
    },

    update(id, payload) {
        return api.put(`/patients/${id}`, payload);
    },

    delete(id) {
        return api.delete(`/patients/${id}`);
    },
};
