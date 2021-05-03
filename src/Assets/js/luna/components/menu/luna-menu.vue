<template>
    <div>
      <template v-for="item in menu">
        <luna-menu-item :item="item" />
      </template>
    </div>
</template>

<script>
import LunaMenuItem from "./luna-menu-item";

export default {
  components: {LunaMenuItem},
  computed: {
    menu() {
      return this.$store.state.menu
    },
  },
}
</script>
