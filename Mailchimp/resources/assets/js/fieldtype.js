Vue.component('mailchimp-fieldtype', {
    template: `
        <div>
            <div v-if="check_permission">
                <div v-if="repopulatingFields" class="loading loading-basic">
                    <span class="icon icon-circular-graph animation-spin"></span> {{ translate('cp.loading') }}
                </div>
                <div v-else>
                    <label class="block" style="font-weight:500">Permission Field</label>
                    <suggest-fieldtype :data.sync="data" :config="suggest_config" :suggestions-prop="fields"></suggest-fieldtype>
                </div>
            </div>
        </div>
    `,

    mixins: [Fieldtype],

    data: function() {
        return {
            repopulatingFields: false,
            index: -1,
            form: null,
            fields: [],
            suggest_config: {
                type: 'suggest',
                max_items: 1
            },
            check_permission: false,
            autoBindChangeWatcher: false // Disable the automagic binding
        }
    },

    methods: {
        loadData: function () {
            this.index = this.name.split('.')[1];
            this.form = this.$parent.data[this.index].form;
            this.check_permission = this.$parent.data[this.index].check_permission;
        },
        loadFormFields: function () {
            let formName = this.form;

            if (formName && formName !== '') {
                this.repopulatingFields = true;

                this.$nextTick(function () {
                    this.$http.get('/!/Mailchimp/fields?form=' + formName, function (data) {
                        this.fields = data;
                        this.repopulatingFields = false;
                    });
                });
            }
        },
        resetFormFields: function () {
            this.fields = [];
            this.data = null;
        },
        bindParentWatcher: function (index) {
            let self = this;
            this.$parent.$watch('data', function (rowData) {
                // check_permissions is on?
                self.check_permission = rowData[self.index].check_permission;
                if (self.form !== rowData[self.index].form) {
                    self.form = rowData[self.index].form;
                    self.loadFormFields();
                }
            }, {deep: true});
        }
    },

    watch: {
        'check_permission': function (doCheck) {
            if (doCheck) {
                this.loadFormFields();
            } else {
                this.resetFormFields();
            }
        }
    },

    ready: function () {
        this.loadData();
        this.loadFormFields();
        this.bindParentWatcher();
        this.bindChangeWatcher(); // Bind manually once you're ready.
    }
});