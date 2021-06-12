<template>
    <div class="mb-3">
        <luna-metric-bar :metrics="metrics" :url-generator="metricUrlGenerator"></luna-metric-bar>

        <h4 v-text="plural" class="mb-3"></h4>

        <luna-data-table ref="dataTable"
                         :resource="resource"
                         :singular="singular"
                         :plural="plural"
                         :fields="visibleFields"
                         :createEnable="createEnable"
                         :createText="createText || `ایجاد ${singular}`"
                         :details-enable="detailsEnable"
                         :edit-enable="editEnable"
                         :remove-enable="removeEnable"
                         :primary-key="primaryKey"
                         :index-column="indexColumn"
                         :search-bar="searchBar"
                         @create="create"
                         @details="details"
                         @edit="edit"
                         @remove="remove">
        </luna-data-table>
    </div>
</template>
<script>
    import resourceMixin from '../mixins/resource'
    import {mapActions} from 'vuex';

    export default {
        mixins: [resourceMixin],
        computed: {
            visibleFields() {
                return _.filter(this.fields, x => x.isVisibleOnIndex)
            }
        },
        methods: {
            ...mapActions([
                'si',
            ]),
            metricUrlGenerator(index) {
                return this.route('luna.resources.metric', [this.resource, index])
            },
            create() {
                this.$router.push({name: 'resources.create', params: {resource: this.resource}})
            },
            details(id) {
                this.$router.push({name: 'resources.details', params: {resource: this.resource, model: id}})
            },
            edit(id) {
                this.$router.push({name: 'resources.edit', params: {resource: this.resource, model: id}})
            },
            remove(id) {
                this.$swal({
                    title: 'آیا مطمئن هستید؟',
                    text: "این عملیات قابل برگشت نیست!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'بله، حذف کنید!',
                    cancelButtonText: 'انصراف',
                    target: document.getElementById('app')
                }).then((result) => {
                    if (result.value) {
                        this.si({
                            url: this.route('luna.resources.destroy', [this.resource, id]),
                            method: 'post'
                        }).then(this.$refs['dataTable'].fetch)
                    }
                })
            },
        }
    }
</script>