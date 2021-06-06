<template>
  <div>
    <template v-if="item.type=='resource'">
      <div class="nav-item">
        <router-link class="nav-link" :to="{name: 'resources', params: {resource: item.resource}}">
          <i class="fa fa-chevron-left"></i>
          <span v-text="item.title"></span>
        </router-link>
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: "luna-menu-link",
  props: {
    item: {
      required: true,
      type: Object
    }
  },
}
</script>
