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
            let key = this.namePrefix.replace('[', '.').replace(']', '.') + 'form.0' ;
            return data_get(this.$store.state.publish[this.storeName].values, key)
        },

        form2() {

            const regex = RegExp(this.escapeRegex("(forms)\[([\d])+\].+"), 'g');
            // const regex = RegExp('\(${this.config.parent_grid}\)[\(\[\d\]\)\+]\.\+`, 'mg');
            let match = regex.exec(this.namePrefix);
            match.shift();


            let key = match.join('.') + `.${this.config.linked_field}.0`;

            console.log(key);
            // return data_get(this.$store.state.publish[this.storeName].values, key)
        },
    },

    mounted() {
        this.selected = this.value;
        this.refreshFields();
    },


    methods: {
        // escapeRegex(value) {
        //     return value.replace( /[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&" );
        // }
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
