import Vue from 'vue';
import Router from 'vue-router';
import { useAuthStore } from '../store/auth';
import pinia from '../plugins/pinia';
import AppLayout from '../components/AppLayout.vue';
import Login from '../views/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import AddressIndex from '../views/enderecos/Index.vue';
import AddressForm from '../views/enderecos/Form.vue';
import PatientIndex from '../views/pacientes/Index.vue';
import PatientForm from '../views/pacientes/Form.vue';
import NotFound from '../views/NotFound.vue';

Vue.use(Router);

const router = new Router({
    mode: 'history',
    routes: [
        { path: '/login', name: 'login', component: Login },
        {
            path: '/',
            component: AppLayout,
            meta: { requiresAuth: true },
            children: [
                { path: '', redirect: '/dashboard' },
                { path: 'dashboard', name: 'dashboard', component: Dashboard },
                { path: 'enderecos', name: 'enderecos.index', component: AddressIndex },
                { path: 'enderecos/novo', name: 'enderecos.create', component: AddressForm },
                { path: 'enderecos/:id', name: 'enderecos.edit', component: AddressForm, props: true },
                { path: 'pacientes', name: 'pacientes.index', component: PatientIndex },
                { path: 'pacientes/novo', name: 'pacientes.create', component: PatientForm },
                { path: 'pacientes/:id', name: 'pacientes.edit', component: PatientForm, props: true },
                { path: '*', name: 'not-found', component: NotFound },
            ],
        },
    ],
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore(pinia);

    if (to.matched.some((route) => route.meta.requiresAuth) && !auth.isAuthenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } });
        return;
    }

    if (to.name === 'login' && auth.isAuthenticated) {
        next({ name: 'dashboard' });
        return;
    }

    next();
});

export default router;
