import TagField from './components/fieldtypes/MailchimpTagFieldtype.vue';
import FormFields from './components/fieldtypes/FormFieldsFieldtype.vue';
import MergeFieldsField from './components/fieldtypes/MailchimpMergeFieldsFieldtype.vue';
import UserFieldsField from './components/fieldtypes/UserFieldsFieldtype.vue';


Statamic.booting(() => {
    Statamic.$components.register('form_field-fieldtype', FormFields);
    Statamic.$components.register('mailchimp_tag-fieldtype', TagField);
    Statamic.$components.register('mailchimp_merge_fields-fieldtype', MergeFieldsField);
    Statamic.$components.register('user_fields-fieldtype', UserFieldsField);
});
