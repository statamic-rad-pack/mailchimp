Vue.component('mailchimp-fieldtype', {
    template: `
        <div>
            <div v-if="loading" class="loading loading-basic">
                <span class="icon icon-circular-graph animation-spin"></span> {{ translate('cp.loading') }}
            </div>

            <div v-else class="row">
                <div class="col-xs-4">Form: <suggest-fieldtype :data.sync="selectedData.form" :config="form_config" name="form" :suggestions-prop="forms"></suggest-fieldtype></div> 
                <div v-if="hasSelectedForm" class="col-xs-4">
                    Check Permission: <toggle-fieldtype :data.sync="selectedData.check_permission" name="check_permission"></toggle-fieldtype>
                </div>
                <div v-if="selectedData.check_permission" class="col-xs-4">
                    Permission Field: <suggest-fieldtype v-ref:permission_field :data.sync="selectedData.permission_field" :config="field_config" name="permission_field" :suggestions-prop="fields"></suggest-fieldtype>
                </div>
        </div>
    `,

    props: ['data', 'config', 'name'],

    data: function() {
        return {
            loading: true,
            forms: [],
            fields: [],
            selectedData: {
                form: (this.data && this.data.form) ? this.data.form : '',
                check_permission: (this.data && this.data.check_permission) ? this.data.check_permission : false,
                permission_field: (this.data && this.data.permission_field) ? this.data.permission_field : '',
            },
            form_config: {
                type: 'suggest',
                max_items: 1
            },
            field_config: {
                type: 'suggest',
                max_items: 1
            },
        }
    },

    computed: {
        hasSelectedForm() {
            return this.data && this.data.form;
        }
    },

    methods: {
        getForms: function(loadFields = false) {
            this.$http.get('/!/Mailchimp/forms', function(data) {
                this.forms = data;
                if (loadFields && this.selectedData.form) {
                    this.getFields(this.selectedData.form[0]);
                }
                this.loading = false;
            });
        },
        getFields: function(formName) {
            if (!formName) {
                this.fields = [];
                this.selectedData.permission_field = '';

                return false;
            }

            let selectedForm = this.forms.filter(function(form) {
                return form.value == formName;
            })[0];

            this.fields = selectedForm.fields;
        },
    },

    watch: {
        'selectedData.form': function() {
            // Reset the fields array before loading new data
            this.getFields();

            // Wait until next tick before repopulating fields array
            this.$nextTick(() => {
                if (this.selectedData.form) {
                    this.getFields(this.selectedData.form[0]);
                    if (!this.data) {
                        this.data = {};
                    }
                    this.data.form = this.selectedData.form;
                }
            });
        },
        'selectedData.permission_field': function() {
            this.data.permission_field = this.selectedData.permission_field;
        },
        'selectedData.check_permission': function() {
            this.data.check_permission = this.selectedData.check_permission;

            if (!this.data.check_permission) {
                this.selectedData.permission_field = null;
            }
        },
    },

    ready: function() {
        this.getForms(true);
    }
});