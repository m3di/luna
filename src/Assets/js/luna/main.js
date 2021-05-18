import ResourceIndex from './components/resource-index'
import ResourceDetails from './components/resource-details'
import ResourceForm from './components/resource-form'

import LunaRelationSelector from './components/relation-selector'
import LunaDataTable from './components/data-table'
import LunaDataForm from './components/data-form'
import LunaFieldFilterDialog from './components/field-filter-dialog'

import LunaTypeText from './components/types/text'
import LunaTypeSwitchText from './components/types/switch-text'
import LunaTypePassword from './components/types/password'
import LunaTypeNumber from './components/types/number'
import LunaTypeEmail from './components/types/email'
import LunaTypeTextarea from './components/types/textarea'
import LunaTypeLiveTextarea from './components/types/live-textarea'
import LunaTypeDatePicker from './components/types/date-picker'
import LunaTypeBoolean from './components/types/boolean'
import LunaTypeBelongsTo from './components/types/belongs_to'
import LunaTypeBelongsToMany from './components/types/belongs_to_many'
import LunaTypeBelongsToManyInline from './components/types/belongs_to_many_inline'
import LunaTypeHasMany from './components/types/has_many'
import LunaTypeHTML from './components/types/html'
import LunaTypeFile from './components/types/file'
import LunaTypeImage from './components/types/image'
import LunaTypeRadio from './components/types/radio'

import LunaMetric from './components/metrics/metric'
import LunaMetricBar from './components/metrics/metric-bar'

import LunaPanelSimple from './components/panels/simple'
import LunaPanelTabbed from './components/panels/tabbed'

import LunaMenu from './components/menu/luna-menu'

export default {
    install(Vue, options) {
        Vue.component('luna-resource-index', ResourceIndex);
        Vue.component('luna-resource-details', ResourceDetails);
        Vue.component('luna-resource-form', ResourceForm);

        Vue.component('luna-relation-selector', LunaRelationSelector);
        Vue.component('luna-data-table', LunaDataTable);
        Vue.component('luna-data-form', LunaDataForm);
        Vue.component('luna-field-filter-dialog', LunaFieldFilterDialog);

        Vue.component('luna-type-text', LunaTypeText);
        Vue.component('luna-type-switch-text', LunaTypeSwitchText);
        Vue.component('luna-type-password', LunaTypePassword);
        Vue.component('luna-type-number', LunaTypeNumber);
        Vue.component('luna-type-email', LunaTypeEmail);
        Vue.component('luna-type-textarea', LunaTypeTextarea);
        Vue.component('luna-type-live_textarea', LunaTypeLiveTextarea);
        Vue.component('luna-type-date_picker', LunaTypeDatePicker);
        Vue.component('luna-type-boolean', LunaTypeBoolean);
        Vue.component('luna-type-belongs_to', LunaTypeBelongsTo);
        Vue.component('luna-type-belongs_to_many', LunaTypeBelongsToMany);
        Vue.component('luna-type-belongs_to_many_inline', LunaTypeBelongsToManyInline);
        Vue.component('luna-type-has_many', LunaTypeHasMany);
        Vue.component('luna-type-html', LunaTypeHTML);
        Vue.component('luna-type-file', LunaTypeFile);
        Vue.component('luna-type-image', LunaTypeImage);
        Vue.component('luna-type-radio', LunaTypeRadio);

        Vue.component('luna-metric', LunaMetric);
        Vue.component('luna-metric-bar', LunaMetricBar);

        Vue.component('luna-menu', LunaMenu);

        Vue.component('luna-panel-simple', LunaPanelSimple);
        Vue.component('luna-panel-tabbed', LunaPanelTabbed);

        require('./components/tools/main');
    }
}
