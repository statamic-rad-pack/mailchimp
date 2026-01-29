<template>
    <div class="mailchimp-merge-fields-fieldtype-wrapper">
      <ui-description v-if="!list">{{ __('Select audience') }}</ui-description>

      <ui-combobox
          v-if="showFieldtype && list"
          class="w-full"
          clearable="true"
          :label="__('Choose')"
          v-model="selected"
          :options="fields"
          optionValue="id"
          searchable="true"
      />

    </div>
</template>

<script>
import { FieldtypeMixin as Fieldtype } from '@statamic/cms';
import { publishContextKey } from '@statamic/cms/ui';
export default {

    mixins: [Fieldtype],

    inject: {
        publishContext: { from: publishContextKey },
    },

    data() {
        return {
            fields: [],
            selected: null,
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
        key() {
            return 'mailchimp.settings.audience_id.0';
        },

        list() {
            return this.publishContext.values.value[this.key] ?? '';
        },
    },

    mounted() {
        this.selected = this.value;
        this.refreshFields();
    },

    methods: {
        refreshFields() {
            if (! this.list) {
                this.tags = [];
                return;
            }

            this.$axios
                .get(cp_url(`/mailchimp/merge-fields/${this.list}`))
                .then(response => {
                    this.fields = response.data;
                })
                .catch(() => { this.fields = []; });
        }
    },

    watch: {
      selected(selected) {
        this.update(selected);
      }
    }
};
</script>
