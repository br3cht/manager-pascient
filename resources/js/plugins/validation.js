import { extend, ValidationObserver, ValidationProvider, localize } from 'vee-validate';
import { required, min, max, length, email } from 'vee-validate/dist/rules';
import pt_BR from 'vee-validate/dist/locale/pt_BR.json';
import Vue from 'vue';

extend('required', required);
extend('min', min);
extend('max', max);
extend('length', length);
extend('email', email);

extend('cpf', {
    validate(value) {
        const digits = String(value || '').replace(/\D/g, '');
        if (digits.length !== 11) return false;
        if (/^(\d)\1+$/.test(digits)) return false;

        const calc = (slice) => {
            const factor = slice.length + 1;
            const sum = slice
                .split('')
                .reduce((acc, char, index) => acc + Number(char) * (factor - index), 0);
            const mod = (sum * 10) % 11;
            return mod === 10 ? 0 : mod;
        };

        return calc(digits.slice(0, 9)) === Number(digits[9])
            && calc(digits.slice(0, 10)) === Number(digits[10]);
    },
    message: 'CPF invalido.',
});

extend('cns', {
    validate(value) {
        const digits = String(value || '').replace(/\D/g, '');
        return digits.length === 15;
    },
    message: 'CNS deve conter 15 digitos.',
});

extend('cep', {
    validate(value) {
        const digits = String(value || '').replace(/\D/g, '');
        return digits.length === 8;
    },
    message: 'CEP deve conter 8 digitos.',
});

extend('phone_br', {
    validate(value) {
        const digits = String(value || '').replace(/\D/g, '');
        if (!digits) return true;
        return (digits.length === 10 || digits.length === 11) && digits.slice(0, 2) !== '00';
    },
    message: 'Telefone deve conter DDD e 8 ou 9 digitos.',
});

extend('not_future', {
    validate(value) {
        if (!value) return true;
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return false;
        const today = new Date();
        today.setHours(23, 59, 59, 999);
        return date <= today;
    },
    message: 'A data nao pode ser futura.',
});

localize('pt_BR', pt_BR);

Vue.component('ValidationObserver', ValidationObserver);
Vue.component('ValidationProvider', ValidationProvider);
