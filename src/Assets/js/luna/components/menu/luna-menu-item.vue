<template>
  <div>
    <template v-if="item.type=='group'">
      <div class="p-2 mx-2 mb-1 sidebar-heading border rounded">
        <h6 class="p-0 m-0 d-flex justify-content-start align-items-center">
          <i class="mr-2" :class="item.icon"></i>
          <span v-text="item.title"></span>
        </h6>
      </div>

      <div class="nav flex-column mb-2">
        <template v-for="child in item.items">
          <luna-menu-item :item="child" />
        </template>
      </div>
    </template>

    <template v-if="item.type=='resource'">
      <div class="nav-item">
        <router-link class="nav-link" :to="{name: 'resources', params: {resource: item.resource}}">
          <span v-text="item.title"></span>
        </router-link>
      </div>
    </template>

    <template v-if="item.type=='resource-bag'">
      <div class="nav-item">
        <a href="javascript:void(0)" class="nav-link dd" :class="{active:active}" @click="active=(!active)">
          <span v-text="item.title"></span>
          <i class="fa float-left" :class="{'fa-caret-left':!active, 'fa-caret-down':active}"></i>
        </a>
      </div>

      <div class="nav flex-column ml-3 mb-2" v-if="active">
        <template v-for="child in item.items">
          <luna-menu-item :item="child" />
        </template>
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: "luna-menu-item",
  props: {
    item: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      active: false
    }
  }
}
</script>
