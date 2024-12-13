<template>
  <navBarVue :users="true" :title="'utilisateurs'" class="z-2 shadow" />
  <div class="m-3 container m-auto position-relative w-100 p-0 z-1">
    <div class="header users-header d-flex rounded-4 position-relative align-items-center bg-white px-2 shadow w-100"
      style="width: 99%">
      <div class="z-2 w-50">
        <div>
          <input type="search" class="search-bar rounded-5 bg-secondary form-control text-white ps-3 pe-0" id="search"
            placeholder="Rechercher un utilisateur..." v-model="searchWord" />
        </div>
      </div>
      <div class="container bg-white py-2 rounded-4 options w-50 d-flex">
        <span colspan="2" class="d-flex alingn-items-center justify-content-center ms-auto">
          <router-link to="/admin/users/Ajouter"
            class="nav-link text-primary mx-2 bg-white p-2 rounded-5 shadow option text-center"
            title="Ajouter un utilisateur">
            <fai icon="user-plus" class="pe-2" />
            <span class="option-title">Ajouter</span>
          </router-link>

          <button class="nav-link text-success me-2 bg-white p-2 rounded-5 shadow option" title="Importer"
            @click="$refs.inputFile.click()">
            <input type="file" @change="importUsers" class="form-control d-none" ref="inputFile"
              accept=".xlsx,.csv,.xls" />

            <fai v-if="!chargement" icon="cloud-arrow-down" class="pe-2" />
            <span v-else class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
            </span>
            <span class="option-title">Importer</span>
          </button>

          <button class="nav-link text-success me-2 bg-white p-2 rounded-5 shadow option" title="Exporter"
            @click.prevent="exportUsers()" :disabled="telechargement ? true : false">
            <fai v-if="!telechargement" icon="cloud-arrow-up" class="pe-2" />
            <span v-else class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
            </span>
            Exporter
          </button>
          <button class="nav-link text-success bg-white p-2 rounded-5 shadow option" title="Telecharger un model"
            @click.prevent="exportTemplate()" :class="telechargementTemplate ? 'desabled' : ''">
            <fai v-if="!telechargementTemplate" icon="file-export" class="pe-2" />
            <span v-else class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
            </span>
            Template
          </button>
        </span>
        <span colspan="2">
          <span class="alert alert-danger p-4 position-absolute file-error" v-if="error">
            {{ error }}
            <button type="button" class="btn-close ms-auto" aria-label="Close" @click="error = ''"></button>
          </span>
        </span>
      </div>
    </div>
    <div class="users-box position-relative" style="min-height: 100vh;">
      <table class="table table-hover shadow table-striped">
        <thead class="thead-dark">
          <tr>
            <th class="text-center text-tuncate" scope="col">Noms</th>
            <th class="text-center text-tuncate" scope="col">Role</th>
            <th class="text-center text-tuncate" scope="col">Email</th>
            <th class="text-center text-tuncate" scope="col w-auto">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filtre.length" v-for="(user, index) in filtre" :key="index">
            <td class="text-center text-truncate d-flex" scope="row">
              {{ user.name }}
              <p class="badge badge-pill bg-primary ms-2 cursor-pointer" data-bs-toggle="modal"
                data-bs-target="#staticBackdrop" @click.prevent="dispApps(user.userID, user.name)">
                {{ user.apps.length }} application(s)
              </p>
            </td>
            <td class="text-center text-truncate" scope="row">
              {{ user.role.roleTitle }}
            </td>
            <td class="text-center col-md-6" scope="row">{{ user.email }}</td>
            <td class="d-flex align-items-center justify-content-center m-auto">
              <div class="icons btn-group text-center m-auto">
                <router-link :to="'/admin/users/Editer?user=' + JSON.stringify(user)" class="btn bg-success text-white">
                  <fai icon="edit"></fai>
                </router-link>
                <button class="btn btn-outline-primary" @click.prevent="dispApps(user.userID, user.name)" id="dismiss1"
                  data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                  <fai icon="fa-list-alt"></fai>
                </button>
                <button class="btn btn-danger" @click.prevent="delUser(user)">
                  <fai icon="trash" v-if="!user.isLoadingDelete"></fai>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  </span>
                </button>
              </div>

              <div class="form-check form-switch">
                <input v-if="!user.isSwiting" class="form-check-input cursor-pointer" type="checkbox"
                  @click="switchState(user)" :checked="user.status ? true : false" />
                <span v-else class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true">
                </span>
              </div>
            </td>
          </tr>
          <tr v-if="isLoadingUsers">
            <th colspan="4">
              <div class="d-flex align-items-center text-secondary">
                <strong>Chargement...</strong>
                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
              </div>
            </th>
          </tr>
          <tr v-if="!filtre.length && !isLoadingUsers">
            <td colspan="4">
              <h3 class="text-warning fw-bold">Aucun utilisateur trouve !</h3>
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
              Applications de
              <span v-if="!isLoadingApps"> {{ userName }}</span>
              <span v-else>
                <div class="spinner-border spinner-border-sm text-secondary mx-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </span>
            </p>
            <button class="btn bg-warning btn-link text-white p-0 mx-2">
              <span v-if="!isLoadingApps">{{ nbApps }}</span>
              <span v-else><span class="spinner-border spinner-border-sm" role="status"
                  aria-hidden="true"></span></span>
            </button>
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="apps.length"
              v-for="(app, index) in apps" :key="index">
              <b>{{ app.appName }}</b>
              <button class="btn badge bg-danger border-none badge-pill w-25"
                @click.prevent="delAppOfUser(user_id, app.appID)">
                <fai icon="trash" />
              </button>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="isLoadingApps">
              <div class="d-flex align-items-center text-secondary">
                <strong>Chargement...</strong>
                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center"
              v-if="!apps.length && !isLoadingApps">
              <p>Aucune application pour {{ userName }}</p>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <button class="btn btn-link bg-primary text-white" title="Ajouter des utilisateurs"
                @click.prevent="getNonApps()" id="dismiss2" data-bs-dismiss="modal" data-bs-toggle="modal"
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
    aria-labelledby="staticBackdrop1Label" aria-hidden="true" v-if="nonApps">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdrop1Label">
            <p>
              Ajouter des application à
              <span v-if="!isLoadingUsers">{{ userName }}</span>
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
              v-if="nonApps.length && !isLoadingNonApps" v-for="(app, index) in nonApps" :key="index">
              <label :for="'app-' + app.appID" class="w-100">
                {{ app.appName }}</label>
              <input type="checkbox" :value="app.appID" :id="'app-' + app.appID" v-model="selectedApps" />
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="isLoadingNonApps">
              <div class="d-flex align-items-center text-secondary">
                <strong>Chargement...</strong>
                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center"
              v-if="!nonApps.length && !isLoadingNonApps">
              <p>{{ userName }} a deja acces a toutes les application</p>
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

const apps = ref([])
const users = ref([])
const userName = ref()
const user_id = ref()
const nonApps = ref([])
const selectedApps = ref([])
const nbApps = ref()
const searchWord = ref(null)
const error = ref(null)

const isSwiting = ref(false)
const isLoadingDelete = ref(false)
const isLoadingApps = ref(true)
const isLoadingUsers = ref(true)
const isLoadingNonApps = ref(true)
const telechargement = ref(false)
const telechargementTemplate = ref(false)
const chargement = ref(false)

const getUsers = async () => {
  isLoadingUsers.value = true
  const url = '/users/get'
  try {
    const response = await apiClient.get(url)
    console.log(response.data)
    users.value = response.data.users
    users.value.forEach(user => {
      isSwiting[user.userID] = false
      isLoadingDelete[user.userID] = false
    })
  } catch (error) {
    console.error('Erreur lors de la récupération des utilisateurs: ', error)
  } finally {
    isLoadingUsers.value = false
  }
}

const filtre = computed(() => {
  if (!searchWord.value) {
    return users.value
  } else {
    return users.value.filter(user =>
      user.name.toLowerCase().includes(searchWord.value.toLowerCase()),
    )
  }
})

const dispApps = async (userID, Name) => {
  isLoadingApps.value = true
  apps.value = []
  try {
    let url = `/user/get/${userID}/apps`
    const response = await apiClient.get(url)
    apps.value = response.data.apps
    nbApps.value = apps.value.length
    user_id.value = userID
    userName.value = Name
  } catch (error) {
    console.error(error)
  } finally {
    isLoadingApps.value = false
  }
}

const delAppOfUser = async (appID, userID) => {
  try {
    let url = `/app/${userID}/prevent/${appID}`
    await apiClient.delete(url).then(response => {
      if (response.data.code == 200) {
        getUsers()
        dispApps(user_id.value, userName.value)
        alert(response.data.message)
      } else {
        console.error(response.data.message)
        console.log(url)
      }
    })
  } catch (error) {
    console.error(
      "Une erreur est survenue en voulant supprimer l'application: ",
      error,
    )
  }
}

const getNonApps = async () => {
  isLoadingNonApps.value = true
  try {
    let url = `/nonusers/get/${user_id.value}`
    await apiClient.get(url).then(response => {
      if (response.data.code == 200) {
        nonApps.value = response.data.apps
        console.log(nonApps.value)
      } else {
        console.error(response.data.message)
      }
    })
  } catch (error) {
    console.error(
      'Impossible de récupérer les application non autorisees: ',
      error,
    )
  } finally {
    isLoadingNonApps.value = false
  }
}

const allowAppToUsers = async () => {
  if (selectedApps.value.length) {
    try {
      let url = `/app/${user_id.value}/allow`
      let post = new FormData()

      selectedApps.value.forEach((app, index) => {
        post.append(`selectedApps[${index}]`, app)
      })
      await apiClient.post(url, post).then(response => {
        if (response.data.code == 200) {
          dispApps(user_id.value, userName.value)
          getUsers()
          alert(response.data.message)
        } else {
          console.error(response)
        }
      })
    } catch (error) {
      console.error("Erreur survenue lors de l'allocation des apps: ", error)
    }
  }
  console.log(selectedApps.value)
}

const delUser = async user => {
  user.isLoadingDelete = true
  var confirme = confirm(
    'Voulez-vous vraiment supprimer cet utilisateur ? Cette action est irréversible.',
  )
  if (confirme) {
    try {
      let url = `/users/${user.userID}/delete`
      await apiClient.delete(url).then(response => {
        if (response.data.code == 200) {
          getUsers()
        } else {
          console.error(response.data.message)
        }
      })
    } catch (error) {
      console.error('Une erreur est survenue lors de la suppression ', error)
    } finally {
      user.isLoadingDelete = false
    }
  } else {
    user.isLoadingDelete = false
  }
}

const exportUsers = () => {
  let url = '/export-users'
  telechargement.value = true
  apiClient({
    url: url,
    method: 'GET',
    responseType: 'blob',
  })
    .then(response => {
      let url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', 'RHOPENLABSUsers.xlsx')
      document.body.appendChild(link)
      link.click()
      document.removeChild(link)
    })
    .catch(error => {
      console.error(error)
    })
    .finally(() => {
      telechargement.value = false
    })
}

const exportTemplate = () => {
  let url = '/export-template'
  telechargementTemplate.value = true
  apiClient({
    url: url,
    method: 'GET',
    responseType: 'blob',
  })
    .then(response => {
      let url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', 'users_model_document_RHopenlabs.xlsx')
      document.body.appendChild(link)
      link.click()
      document.removeChild(link)
    })
    .catch(error => {
      console.error(error)
    })
    .finally(() => {
      telechargementTemplate.value = false
    })
}

const importUsers = async event => {
  chargement.value = true
  const file = event.target.files[0]

  if (file) {
    const validTypes = [
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'text/csv',
      'application/vnd.ms-excel',
      'application/vnd.ms-excel.sheet.macroEnabled.12',
      'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
    ]
    if (!validTypes.includes(file.type)) {
      console.log('file', file.type)
      error.value = 'Type de fichier invalide (*.xlsx,*.csv,*.xls)'
      chargement.value = false
    } else {
      let formData = new FormData()

      formData.append('file', file)

      await apiClient
        .post('/import-users', formData)
        .then((response) => {
          if (response.data.code == 200) {
            alert(response.data.message)
            getUsers()
          } else {
            error.value = response.data.message
            console.error(response)
          }
        })
        .catch(e => {
          error.value = e.code == "ERR_BAD_RESPONSE" ? 'Le modele du fichie est invalide. Vous pouvez telecharger le template' : e.response.data.message
          console.error(e)
        }).finally(() => {
          chargement.value = false
        })
    }
  }
}

const switchState = async user => {
  user.isSwiting = true
  let url = `/switchState/${user.userID}`
  await apiClient
    .post(url)
    .then(() => {
      getUsers()
    })
    .catch(error => {
      console.error(error)
      alert('Une erreur est survenue')
    })
    .finally(() => {
      user.isSwiting = false
    })
}

onMounted(() => {
  getUsers()
})
</script>
