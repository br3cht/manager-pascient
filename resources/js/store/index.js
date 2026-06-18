import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import addresses from './modules/addresses';
import patients from './modules/patients';
import dashboard from './modules/dashboard';
import feedback from './modules/feedback';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        addresses,
        patients,
        dashboard,
        feedback,
    },
});
