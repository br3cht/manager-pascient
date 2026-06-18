import '../css/app.css';
import Vue from 'vue';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import pinia from './plugins/pinia';
import './plugins/validation';
import './services/api';

Vue.config.productionTip = false;

new Vue({
    router,
    vuetify,
    pinia,
    render: (h) => h(App),
}).$mount('#app');
