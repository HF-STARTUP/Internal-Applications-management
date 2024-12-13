<template>
  <router-view />
  <!-- <navBar v-if="" /> -->
  <footerVue />
</template>

<script>
import footerVue from './components/footer.vue';
import navBar from './components/admin/navBar.vue';
export default {
  components: {
    footerVue,
    navBar
  }
}
</script>

