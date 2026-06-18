import Vue from 'vue';
import { PiniaVuePlugin, createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import './plugins/validation';
import './services/api';

Vue.config.productionTip = false;
Vue.use(PiniaVuePlugin);

new Vue({
    router,
    vuetify,
    pinia: createPinia(),
    render: (h) => h(App),
}).$mount('#app');
