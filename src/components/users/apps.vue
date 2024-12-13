<template>
  <div class="p-0 m-0" style="min-height: 100vh;">
    <NavBar :title="'applications'" v-if="user.is_admin" :accueil="true" />
    <div
      class="container d-flex align-items-center justify-content-center position-relative mt-0 border-none bg-white shadow p-4 w-100 header">
      <router-link to="/">
        <img src="../../assets/LOGO-RHOPEN-LABS.png" alt="RH_OpenApps" class="img img-fluid rh-logo cursor-pointer"
          v-if="!user.is_admin" />
      </router-link>
      <div class="container container-fluid">
        <div class="d-flex align-items-center justify-content-center w-100">
          <div class="col-md-6 position-relative">
            <div>
              <input type="search" class="search-bar rounded-5 border-0 bg-secondary form-control text-white ps-5 w-100"
                placeholder="Rechercher une application..." v-model="searchWord" />
            </div>
            <div class="position-absolute ms-2 px-1 fs-6 text-white search-icon">
              <fai icon="search" />
            </div>
          </div>
        </div>
      </div>
      <div class="d-flex" v-if="!user.is_admin">
        <fai icon="user" class="text-white text-center bg-black pt-2 px-2 rounded-5 fs-2" />
        <span class="ms-2">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
              data-bs-toggle="dropdown" aria-expanded="false">
              <span v-if="!authStore.isDisconnecting">
                {{ user.name }}
              </span>
              <span v-else>
                <button class="btn btn-primary" type="button" disabled>
                  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  Deconnexion...
                </button>
              </span>
            </button>
            <ul class="dropdown-menu bg-warning" aria-labelledby="dropdownMenuButton1">
              <li>
                <span class="dropdown-item fw-bold cursor-pointer">
                  <span>{{ user.email }}</span>
                </span>
              </li>
              <li @click.prevent="authStore.logout()">
                <span class="dropdown-item fw-bold cursor-pointer">
                  <fai icon="sign-out" class="mx-2 text-error fw-bold"></fai>
                  <span>Deconnexion</span>
                </span>
              </li>
              <li>
                <router-link to="/updatePassword" class="dropdown-item fw-bold cursor-pointer">
                  <fai icon="lock" class="mx-2 text-rh fw-bold"></fai>
                  <span class="text-rh text-bold">Mot de passe</span>
                </router-link>
              </li>
            </ul>
          </div>
        </span>
      </div>
    </div>
    <main>
      <div class="container mt-5">
        <div class="row row-cols-5 row-cols-lg-6 d-flex align-items-center justify-content-center">
          <div class="col col-app" v-for="(app, index) in filtre" :key="index" v-if="filtre.length">
            <a :href="app.appLink" target="_blank" class="box-shadow">
              <div class="img-box rounded-5 shadow d-flex align-items-center justify-content-center opacity-100">
                <img :src="backendServer + app.appImage" :alt="app.appName" class="img img-fluid app-icon rounded-5" />
              </div>
            </a>
            <p class="text-center text-fluid text-truncate img-title mt-1 fs-md-4 text-secondary">
              {{ app.appName }}
            </p>
          </div>
          <div class="col col-app" v-if="isLoadingApps">
            <div class="box-shadow">
              <div class="img-box rounded-5 shadow d-flex align-items-center justify-content-center opacity-100">
                <div class="spinner-border" style="width: 3vw; height: 3vw" role="status"></div>
              </div>
            </div>
            <p class="text-center text-fluid text-truncate img-title mt-1 fs-md-4 text-dark fw-bold">
              Chargement...
            </p>
          </div>
        </div>

        <div v-if="!filtre.length && !isLoadingApps">
          <h1 class="text-secondary text-center bg-white p-2 rounded-4 shadow fs-6 fw-bold">
            Aucune application afficher. Veuillez rencontrer l'administrateur.
          </h1>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api'
import NavBar from '../admin/navBar.vue'
const apps = ref([])
const authStore = useAuthStore()
const user = computed(() => authStore.user)
const searchWord = ref('')
const backendServer = authStore.backendServer;

const isLoadingApps = ref(false)

const getApps = async () => {
  isLoadingApps.value = true
  const url = user.value.is_admin
    ? 'apps/get'
    : `user/get/${user.value.userID}/apps`
  try {
    const response = await apiClient.get(url)
    console.log(response.data)
    apps.value = response.data.apps
  } catch (error) {
    console.error('Erreur lors de la récupération des applications: ', error)
  } finally {
    isLoadingApps.value = false
  }
  console.log(authStore.backendServer)
}

const filtre = computed(() => {
  if (!searchWord.value) {
    return apps.value
  } else {
    return apps.value.filter(app =>
      app.appName.toLowerCase().includes(searchWord.value.toLowerCase()),
    )
  }
})

onMounted(() => {
  getApps()
})
</script>
