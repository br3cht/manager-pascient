import api from './api';

export default {
    login(credentials) {
        return api.post('/login', {
            ...credentials,
            device: navigator.userAgent || 'spa',
        });
    },

    logout() {
        return api.post('/logout');
    },
};
