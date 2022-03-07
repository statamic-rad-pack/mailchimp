<template>
    <div class="mailchimp-merge-fields-fieldtype-wrapper">
        <small class="help-block text-grey-60" v-if="!list">{{ __('Select audience') }}</small>

        <v-select
            v-if="showFieldtype && list"
            :value="value"
            :clearable="true"
            :searchable="true"
            :options="fields"
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
            fields: [],
            showFieldtype: true,
        }
    },

    watch: {
        list(list) {
            this.showFieldtype = false;

            this.refreshFields();

            this.$nextTick(() => this.showFieldtype = true);
        }
    },

    computed: {
        list() {
            let regexpNames =  /(forms)\[([\d])+\].+/mg;
            let match = regexpNames.exec(this.namePrefix);
            match.shift();


            let key = match.join('.') + '.audience_id.0';
            return data_get(this.$store.state.publish[this.storeName].values, key)
        },
    },

    mounted() {
        this.refreshFields();
    },

    methods: {
        refreshFields() {
            this.$axios.get(cp_url(`/mailchimp/merge-fields/${this.list}`)).then(response => {
                this.fields = response.data;
            });
        }
    }
};
</script>
