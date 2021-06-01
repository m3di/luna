<template>
  <div v-html="value"></div>
</template>

<script>
import lunaType from '../../mixins/input'

export default {
  mixins: [lunaType],
}
</script>