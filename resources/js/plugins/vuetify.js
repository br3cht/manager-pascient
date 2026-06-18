import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        themes: {
            light: {
                primary: '#005CA9',
                secondary: '#1F8ACB',
                accent: '#00A3A3',
                error: '#B00020',
                background: '#F4F8FB',
            },
        },
    },
});
