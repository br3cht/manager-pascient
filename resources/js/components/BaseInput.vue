<template>
    <ValidationProvider v-slot="{ errors }" :name="label" :rules="rules" slim>
        <v-text-field
            :value="displayValue"
            :label="label"
            :type="type"
            :placeholder="placeholder"
            :error-messages="errors"
            :disabled="disabled"
            :loading="loading"
            outlined
            dense
            hide-details="auto"
            @input="onInput"
            @blur="$emit('blur')"
        />
    </ValidationProvider>
</template>

<script>
const masks = {
    cpf: '000.000.000-00',
    cep: '00000-000',
    cns: '000 0000 0000 0000',
    phone: '(00) 00000-0000',
};

function onlyDigits(value) {
    return String(value || '').replace(/\D/g, '');
}

function maxDigits(pattern) {
    if (!pattern) {
        return Infinity;
    }

    let count = 0;
    for (const char of pattern) {
        if (char === '0') count += 1;
    }
    return count;
}

function applyMask(value, pattern) {
    if (!pattern) {
        return value;
    }

    const digits = onlyDigits(value);
    let output = '';
    let index = 0;

    for (const char of pattern) {
        if (char === '0') {
            if (digits[index] === undefined) break;
            output += digits[index];
            index += 1;
        } else if (digits[index] !== undefined) {
            output += char;
        }
    }

    return output;
}

export default {
    name: 'BaseInput',

    props: {
        value: { type: [String, Number], default: '' },
        label: { type: String, required: true },
        rules: { type: [String, Object], default: '' },
        mask: { type: String, default: null },
        type: { type: String, default: 'text' },
        placeholder: { type: String, default: '' },
        disabled: { type: Boolean, default: false },
        loading: { type: Boolean, default: false },
    },

    computed: {
        displayValue() {
            const pattern = masks[this.mask] || this.mask;
            return pattern ? applyMask(this.value, pattern) : this.value;
        },
    },

    methods: {
        onInput(value) {
            if (!this.mask) {
                this.$emit('input', value);
                return;
            }

            const pattern = masks[this.mask] || this.mask;
            this.$emit('input', onlyDigits(value).slice(0, maxDigits(pattern)));
        },
    },
};
</script>
