<template>
    <div class="d-flex align-items-stretch" v-if="metrics.length > 0">
        <div class="col-12 col-md-4 col-lg-3 p-2 mb-3 mx-1 bg-white border rounded"
             v-for="(metric, index) in metrics">
            <luna-metric :metric="metric" :index="index" :url-generator="urlGenerator"></luna-metric>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            metrics: {
                type: Array,
                required: true,
            },
            urlGenerator: {
                type: Function,
                required: true,
            }
        }
    }
</script>