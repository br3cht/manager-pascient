import api from './api';

export default {
    totals() {
        return api.get('/dashboard');
    },
};
