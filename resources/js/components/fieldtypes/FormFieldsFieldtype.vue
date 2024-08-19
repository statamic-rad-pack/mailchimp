<template>
    <div class="form-field-fieldtype-wrapper">
        <small class="help-block text-grey-60" v-if="!form">{{ __('Select form') }}</small>

        <v-select
            append-to-body
            v-if="showFieldtype && form"
            v-model="selected"
            :clearable="true"
            :options="fields"
            :reduce="(option) => option.id"
            :searchable="true"
            @input="$emit('input', $event)"
        />
    </div>
</template>

<script>
export default {

    mixins: [Fieldtype],

    inject: ['storeName'],

    data() {
        return {
            selected: null,
            showFieldtype: true,
            fields: [],
        }
    },

    watch: {
        form(form) {
            this.showFieldtype = false;

            this.refreshFields();

            this.$nextTick(() => this.showFieldtype = true);
        }
    },

    computed: {
        form() {
            return StatamicConfig.urlPath.split('/')[1] ?? '';
        },
    },

    mounted() {
        this.selected = this.value;
        this.refreshFields();
    },


    methods: {
        refreshFields() {
            this.$axios
                .get(cp_url(`/mailchimp/form-fields/${this.form}`))
                .then(response => {
                    this.fields = response.data;
                });
        }
    }
};
</script>
