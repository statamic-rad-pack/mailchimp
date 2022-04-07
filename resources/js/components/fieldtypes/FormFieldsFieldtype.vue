<template>
    <div class="form-field-fieldtype-wrapper">
        <small class="help-block text-grey-60" v-if="!form">{{ __('Select form') }}</small>

        <v-select
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
            let key = 'forms.' + this.row + '.form.0' ;

            return data_get(this.$store.state.publish[this.storeName].values, key)
        },

        row() {
            let matches = this.namePrefix.match(/\[(.*?)\]/);

            return matches[1];
        }
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
