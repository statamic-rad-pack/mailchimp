<template>
    <div class="mailchimp-tag-fieldtype-wrapper">
        <small class="help-block text-grey-60" v-if="!list">{{ __('Select audience') }}</small>

        <relationship-fieldtype
            v-if="showFieldtype && list"
            :handle="handle"
            :value="value"
            :meta="relationshipMeta"
            :config="{ mode: 'select', max_items: 1, type: 'mailchimp_tag' }"
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
        list(list) {
            this.showFieldtype = false;
            this.$nextTick(() => this.showFieldtype = true);
        }
    },

    computed: {
        list() {
            let key = this.namePrefix.replace('[', '.').replace(']', '.') + 'audience_id.0' ;
            return data_get(this.$store.state.publish[this.storeName].values, key)
        },

        relationshipMeta() {
            return {...this.meta, ...{
                getBaseSelectionsUrlParameters: { list: this.list }
            }};
        }
    }
};
</script>
