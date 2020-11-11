<template>
    <div class="form-field-fieldtype-wrapper">
        <small class="help-block text-grey-60" v-if="!form">{{ __('Select form') }}</small>

        <relationship-fieldtype
            v-if="showFieldtype && form"
            :handle="handle"
            :value="value"
            :meta="relationshipMeta"
            :config="{ mode: 'select', max_items: 1, type: 'form_field' }"
            @input="update"
        />
    </div>
</template>

<script>
export default {

    mixins: [Fieldtype],

    inject: ['storeName'],

    data() {
        return {
            showFieldtype: true,
        }
    },

    watch: {
        form(form) {
            this.showFieldtype = false;
            this.$nextTick(() => this.showFieldtype = true);
        }
    },

    computed: {
        form() {

            const regex = RegExp(this.escapeRegex("(forms)\[([\d])+\].+"), 'g');
            // const regex = RegExp('\(${this.config.parent_grid}\)[\(\[\d\]\)\+]\.\+`, 'mg');
            let match = regex.exec(this.namePrefix);
            match.shift();


            let key = match.join('.') + `.${this.config.linked_field}.0`;

            console.log(key);
            // return data_get(this.$store.state.publish[this.storeName].values, key)
        },

        relationshipMeta() {
            return {...this.meta, ...{
                getBaseSelectionsUrlParameters: { form: this.form }
            }};
        }
    },

    methods: {
        escapeRegex(value) {
            return value.replace( /[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&" );
        }
    }
};
</script>
