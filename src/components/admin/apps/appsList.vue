<template>
  <navBarVue :apps="true" :title="'applications'" class="shadow" />
  <div class="m-3 container m-auto position-relative w-100 p-0" style="min-height: 100vh;">
    <div class="header apps-header d-flex align-items-center position-relative bg-white w-100 shadow p-2 rounded-4 z-1">
      <div class="w-50 search">
        <div class="m-auto">
          <input type="search" class="search-bar rounded-5 border-0 bg-secondary form-control text-white ps-3"
            id="search" placeholder="Rechercher une application..." v-model="searchWord" />
        </div>
      </div>
      <div class="w-50 action">
        <router-link to="/admin/applications/Ajouter"
          class="nav-link text-primary d-flex align-items-center justify-content-center fs-2 w-25 ms-auto"
          title="Ajouter une application">
          <fai icon="plus" class="bg-white shadow p-2 rounded-5"></fai>
        </router-link>
      </div>
    </div>
    <div class="users-box position-relative">
      <table class="table table-hover shadow table-striped">
        <thead>
          <tr>
            <th class="text-center text-tuncate" scope="col">Application</th>
            <th class="text-center text-tuncate" scope="col">Liens</th>
            <th class="text-center text-tuncate" scope="col">Icone</th>
            <th class="text-center text-tuncate" scope="col w-auto">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filtre.length" v-for="(app, index) in filtre" :key="index">
            <td class="text-truncate text-center py-5" scope="row">
              {{ app.appName }}
              <p class="badge badge-pill bg-primary ms-2 cursor-pointer" data-bs-toggle="modal"
                data-bs-target="#staticBackdrop" @click.prevent="dispApp(app.appID, app.appName)">
                {{ app.users.length }} utilisateur(s)
              </p>
            </td>
            <td class="text-center text-truncate py-5" scope="row">
              <a :href="app.appLink">{{ app.appLink }}>></a>
            </td>
            <td class="text-center py-4" scope="row">
              <div class="container container-fluid">
                <img :src="backendServer + app.appImage" :alt="app.appName" class="img img-fluid rounded-5"
                  style="min-width: 100px; max-width: 100px" />
              </div>
            </td>
            <td class="py-5 text-center">
              <div class="icons btn-group">
                <router-link :to="'/admin/applications/Editer?appID=' + app.appID" class="btn bg-success text-white">
                  <fai icon="edit"></fai>
                </router-link>
                <button class="btn btn-outline-primary" @click.prevent="dispApp(app.appID, app.appName)" id="dismiss1"
                  data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                  <fai icon="fa-user"></fai>
                </button>
                <button class="btn btn-danger" @click.prevent="delApp(app)">
                  <fai icon="trash" v-if="!app.isLoadingDelete"></fai>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  </span>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="isLoadingApps">
            <th colspan="4">
              <div class="d-flex align-items-center text-secondary">
                <strong>Chargement...</strong>
                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
              </div>
            </th>
          </tr>
          <tr v-if="!filtre.length && !isLoadingApps">
            <td colspan="4">
              <h3 class="text-warning fw-bold">Aucune application trouve !</h3>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" v-if="users">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 d-flex w-75" id="staticBackdropLabel">
            <p>
              Utilisateurs de
              <span v-if="!isLoadingUsers"> {{ appName }}</span>
              <span v-else>
                <div class="spinner-border spinner-border-sm text-secondary mx-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </span>
            </p>
            <button class="btn bg-warning btn-link text-white p-0 mx-2">
              <span v-if="!isLoadingUsers">{{ nbUsers }}</span>
              <span v-else><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              </span>
            </button>
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="users.length"
              v-for="(user, index) in users" :key="index">
              <b>{{ user.name }}</b>
              <button class="btn badge bg-danger border-none badge-pill w-25"
                @click.prevent="delUserOfApp(app_id, user.userID)">
                <fai icon="trash" />
              </button>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="isLoadingUsers">
              <div class="d-flex align-items-center text-secondary">
                <strong>Chargement...</strong>
                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center"
              v-if="!users.length && !isLoadingUsers">
              <p>Aucun utilisateur pour {{ appName }}</p>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <button class="btn btn-link bg-primary text-white" title="Ajouter des utilisateurs"
                @click.prevent="getNonusers()" id="dismiss2" data-bs-dismiss="modal" data-bs-toggle="modal"
                data-bs-target="#staticBackdrop1">
                <fai icon="plus" />
              </button>
            </li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-25" data-bs-dismiss="modal">
            Terminer
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdrop1Label" aria-hidden="true" v-if="nonUsers">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdrop1Label">
            <p>
              Ajouter des utilisateurs à
              <span v-if="!isLoadingUsers">{{ appName }}</span>
              <span v-else>
                <div class="spinner-border spinner-border-sm" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </span>
            </p>
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center"
              v-if="nonUsers.length && !isLoadingNonUsers" v-for="(user, index) in nonUsers" :key="index">
              <label :for="'user-' + user.userID" class="w-100">
                {{ user.name }}</label>
              <input type="checkbox" :value="user.userID" :id="'user-' + user.userID" v-model="selectedUsers" />
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="isLoadingNonUsers">
              <div class="d-flex align-items-center text-secondary">
                <strong>Chargement...</strong>
                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center"
              v-if="!nonUsers.length && !isLoadingNonUsers">
              <p>Tous les utilisateurs ont accès à {{ appName }}</p>
            </li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" id="modal2" class="btn btn-primary w-25" data-bs-dismiss="modal" data-bs-toggle="modal"
            @click.prevent="allowAppToUsers()" data-bs-target="#staticBackdrop">
            Valider
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import navBarVue from '../navBar.vue'
import apiClient from '@/api'
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore()
const backendServer = authStore.backendServer
const apps = ref([])
const users = ref([])
const appName = ref()
const app_id = ref()
const nonUsers = ref([])
const selectedUsers = ref([])
const nbUsers = ref()
const searchWord = ref(null)

const isLoadingApps = ref(true)
const isLoadingUsers = ref(true)
const isLoadingNonUsers = ref(true)
const isLoadingDelete = ref(false)

const getApps = async () => {
  isLoadingApps.value = true
  const url = '/apps/get'
  try {
    const response = await apiClient.get(url)
    console.log(response.data)
    apps.value = response.data.apps
    apps.value.forEach(app => {
      isLoadingDelete[app.appID] = false
    })
  } catch (error) {
    console.error('Erreur lors de la récupération des applications: ', error)
  } finally {
    isLoadingApps.value = false
  }
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

const dispApp = async (appID, Name) => {
  isLoadingUsers.value = true
  users.value = []
  try {
    const response = await apiClient.get(`/apps/get/${appID}/users`)
    users.value = response.data.users
    nbUsers.value = users.value.length
    app_id.value = appID
    appName.value = Name
  } catch (error) {
    console.error(error)
  } finally {
    isLoadingUsers.value = false
  }
}

const delUserOfApp = async (appID, userID) => {
  try {
    let url = `/app/${appID}/prevent/${userID}`
    await apiClient.delete(url).then(response => {
      if (response.data.code == 200) {
        alert(response.data.message)
        dispApp(app_id.value, appName.value)
      } else {
        console.error(response.data.message)
        console.log(url)
      }
    })
  } catch (error) {
    console.error(
      "Une erreur est survenue en voulant supprimer l'utilisateur: ",
      error,
    )
  }
}

const getNonusers = async () => {
  isLoadingNonUsers.value = true
  try {
    let url = `/nonusers/get/${app_id.value}`
    await apiClient.get(url).then(response => {
      if (response.data.code == 200) {
        nonUsers.value = response.data.users
        console.log(nonUsers.value)
      } else {
        console.error(response.data.message)
      }
    })
  } catch (error) {
    console.error('Impossible de récupérer les non utilisateurs: ', error)
  } finally {
    isLoadingNonUsers.value = false
  }
}

const allowAppToUsers = async () => {
  if (selectedUsers.value.length) {
    try {
      let url = `/app/${app_id.value}/allow`
      let post = new FormData()

      selectedUsers.value.forEach((user, index) => {
        post.append(`selectedUsers[${index}]`, user)
      })
      await apiClient.post(url, post).then(response => {
        if (response.data.code == 200) {
          dispApp(app_id.value, appName.value)
          alert(response.data.message)
        } else {
          console.error(response)
        }
      })
    } catch (error) {
      console.error("Erreur survenue lors de l'allocation des app: ", error)
    }
  }
  console.log(selectedUsers.value)
}

const delApp = async (app) => {
  app.isLoadingDelete = true
  var confirme = confirm(
    'Voulez-vous vraiment supprimer cette application ? Cette action est irréversible.',
  )
  if (confirme) {
    try {
      let url = `/apps/${app.appID}/delete`
      await apiClient.delete(url).then(response => {
        if (response.data.code == 200) {
          getApps()
          alert(response.data.message)
        } else {
          console.error(response.data.message)
        }
      })
    } catch (error) {
      console.error('Une erreur est survenue lors de la suppression ', error)
    } finally {
      app.isLoadingDelete = false
    }
  } else {
    app.isLoadingDelete = false
  }
}
onMounted(() => {
  getApps()
})
</script>
