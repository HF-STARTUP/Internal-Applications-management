<template>
  <nav class="navbar navbar-expand-lg bg-white px-0 border-none position-relative" data-bs-theme="dark" style="z-index: 10;">
    <div class="container-fluid">
      <router-link to="/" class="navbar-brand w-25 p-2">
        <img src="@/assets/LOGO-RHOPEN-LABS.png" alt="RH_OpenApps"
          class="img img-fluid me-auto rh-logo cursor-pointer" />
      </router-link>
      <button class="navbar-toggler collapsed bg-purple" @click="showHide()" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false"
        aria-label="Toggle navigation">
        <span>
          <fai icon="chevron-up" class="text-white" v-if="iconDirection == 'up'" />
          <fai icon="chevron-down" class="text-white" v-else />
        </span>
      </button>

      <div class="navbar-collapse collapse w-50" id="navbarColor01" style="">
        <ul class="navbar-nav m-auto">
          <li class="nav-item">
            <router-link :class="[accueil ? 'active' : '', 'nav-link']" to="/applications">
              <fai icon="home" /> Accueil
            </router-link>
          </li>
          <li class="nav-item">
            <router-link :class="[users ? 'active' : '', 'nav-link']" to="/admin/users">
              <fai icon="users" /> Utilisateurs
            </router-link>
          </li>
          <li class="nav-item">
            <router-link :class="[apps ? 'active' : '', 'nav-link']" to="/admin/applications">
              <fai icon="list" /> Applications
            </router-link>
          </li>
          <li class="nav-item">
            <router-link :class="[roles ? 'active' : '', 'nav-link']" to="/admin/roles?action:add">
              <fai icon="briefcase" /> Roles
            </router-link>
          </li>
        </ul>
      </div>
    </div>
    <div class="m-auto mt-3 mx-lg-5 nav-item">
      <div class="d-flex w-50">
        <fai icon="user" class="text-white text-center bg-black p-2 rounded-5 fs-3" />
        <div class="dropdown ms-2">
          <button class="btn btn-rh shadow dropdown-toggle" type="button" id="dropdownMenuButton1"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span v-if="!store.isDisconnecting">
              {{ user.name }}
            </span>
            <span v-else>
              <button class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Deconnexion...
              </button>
            </span>
          </button>
          <ul class="dropdown-menu bg-warning me-auto position-realative z-2" aria-labelledby="dropdownMenuButton1">
            <li @click.prevent="store.logout()">
              <span class="dropdown-item fw-bolder cursor-pointer text-secondary">
                <fai icon="sign-out" class="mx-2 fw-bold"></fai>
                <span>Deconnexion</span>
              </span>
            </li>
            <li>
              <router-link to="/updatePassword" class="dropdown-item fw-bolder cursor-pointer text-secondary">
                <fai icon="lock" class="mx-2 fw-bold text-rh"></fai>
                <span class="text-rh">Mot de passe</span>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>
<script setup>
import { useAuthStore } from '@/stores/auth';
import { ref } from 'vue';

const iconDirection = ref('down');
const store = useAuthStore();
const user = store.user;

const showHide = () => {
  iconDirection.value = iconDirection.value == 'down' ? 'up' : 'down';
}
const props = defineProps({
  users: Boolean,
  apps: Boolean,
  roles: Boolean,
  accueil: Boolean,
  title: {
    type: String,
    required: true,
  },
})
</script>

<style></style>
