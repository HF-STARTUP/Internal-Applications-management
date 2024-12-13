<template>
  <navBarVue :roles="true" :title="'roles'" />
  <div class="container container-fluid p-0 w-100" style="min-height: 100vh;">
    <router-link @click="scrollToForm(), (roleForm = true), (roleValue = '')"
      :to="{ path: '/admin/roles', hash: '#roleForm', query: { action: 'add' } }"
      class="btn btn-primary btns z-2 fs-5 p-2 my-4 m-auto w-25 w-sm-50  header">
      <fai icon="plus" class="fw-bolder" />
      <span> Ajouter un rôle </span>
    </router-link>
    <div class="col-md-6 m-auto text-center">
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center" v-for="(role, index) in roles"
          :key="index">
          <span>{{ role.roleTitle }}
            <span class="badge badge-pill bg-primary">{{
              role.users.length
              }}</span></span>
          <div class="badge badge-pill">
            <div class="btn-group">
              <router-link :to="{
                path: '/admin/roles',
                hash: '#roleForm',
                query: {
                  action: 'edit',
                  id: role.roleID,
                  role: role.roleTitle,
                },
              }" @click.prevent="scrollToForm(), (roleValue = route.query.role)" class="btn btn-outline-secondary">
                <fai icon="edit" />
              </router-link>
              <button class="btn btn-outline-secondary" @click="
                users = role.users,
                roleTitle = role.roleTitle
                " data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <fai icon="user" />
              </button>
              <button class="btn btn-outline-danger" @click="delRole(role.roleID)">
                <fai icon="trash" />
              </button>
            </div>
          </div>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center" v-if="isLoadingRoles">
          <div class="d-flex align-items-center text-secondary">
            <strong>Chargement...</strong>
            <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
          </div>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center"
          v-if="!isLoadingRoles && roles.length === 0">
          <div class="d-flex align-items-center text-secondary bg-white shadow p-1 m-auto rounded-4 fw-bold px-4">
            Aucun rôle n'a été trouvé
          </div>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center" id="roleForm" v-if="roleForm">
          <form class="btn-group w-75" method="post" @submit.prevent="tipeRole()">
            <input type="text" class="form-control" placeholder="Nom du rôle..." v-model="roleValue" min="1" required />
            <button type="submit" class="btn btn-primary w-50 fs-6 w-25 btns">
              OK
            </button>
          </form>
          <span>
            <button type="button" class="btn-close" aria-label="Close" @click="roleForm = false"></button>
          </span>
        </li>
      </ul>
    </div>
  </div>
  <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" v-if="users">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 d-flex w-75" id="staticBackdropLabel">
            <p>
              Liste des
              <span> {{ roleTitle }}</span>
            </p>
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="users.length"
              v-for="(user, index) in users" :key="index">
              <b>{{ user.name }}</b>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center" v-if="!users.length">
              <p class="m-auto text-center bg-white shadow p-2 px-4 rounded-4 text-secondary">
                Nous n'avons pas encore de {{ roleTitle }}
              </p>
            </li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-25" data-bs-dismiss="modal">
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { onMounted, ref } from 'vue'
import navBarVue from '../navBar.vue'
import apiClient from '@/api'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()

const isLoadingRoles = ref(false)
const roleForm = ref(false)
const roles = ref([])
const role = ref('')
const roleValue = ref('')
const users = ref([])
const roleTitle = ref('')

const getRole = async () => {
  isLoadingRoles.value = true
  roles.value = []
  try {
    const response = await apiClient.get('/roles/get')
    roles.value = response.data.roles
  } catch (error) {
    console.error(error)
  } finally {
    isLoadingRoles.value = false
  }
}

const delRole = async roleID => {
  let url = `/roles/${roleID}/delete`
  let confirmation = confirm(
    'Tous les utilisateurs ayant ce role seront aussi supprimer. Voulez vous continuer?',
  )
  if (confirmation) {
    await apiClient.delete(url).then(response => {
      getRole()
      alert(response.data.message)
    })
  } else {
    alert("Aucun element n' ete supprimer...")
  }
}

const tipeRole = async () => {
  var url = ref('')
  if (route.query && route.query.action === 'edit' && route.query.id) {
    url.value = `/roles/${route.query.id}/edit`
  } else if (route.query && route.query.action === 'add') {
    url.value = '/roles/add'
  } else {
    router.back()
  }
  const data = new FormData()
  let trimRole = roleValue.value.trim()
  if (trimRole !== '') {
    data.append('roleTitle', roleValue.value)
  } else {
    alert('Ce champ ne doit pas etre vide')
    return
  }
  try {
    await apiClient.post(url.value, data).then(response => {
      getRole()
      router.back()
      roleForm.value = false
      role.value = ''
      alert(response.data.message)
    })
  } catch (error) {
    console.error(error)
  }
}

const scrollToForm = () => {
  roleForm.value = true
  const element = document.getElementById('roleForm')
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
}

onMounted(() => {
  getRole()
});
</script>

<style>
#roleForm {
  animation: formRole 1s alternate;
}

@keyframes formRole {
  0% {
    transform: translateY(-10px);
    opacity: 0;
  }

  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

.header.position-relative {
  margin-top: 10px;
}
</style>
