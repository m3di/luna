<template>
  <div id="luna-menu">
    <template v-for="(item, i) in menu">
      <component :item="item" :is="'luna-menu-' + item.c" @select="selected(i)" ref="items"></component>
    </template>
  </div>
</template>

<script>

import LunaMenuLink from "./luna-menu-link";
import LunaMenuGroup from "./luna-menu-group";

export default {
  components: {LunaMenuGroup, LunaMenuLink},
  methods: {
    selected(i) {
      for (let x in this.$refs.items) {
        if (x != i && this.$refs.items[x].hasOwnProperty('hide'))
          this.$refs.items[x].hide()
      }
      this.$refs.items[i].show()
    }
  },
  computed: {
    menu() {
      return this.$store.state.menu
    },
  },
}
</script>
