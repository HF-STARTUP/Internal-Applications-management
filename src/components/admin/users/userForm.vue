<template>
  <navBar :users="true" :title="'utilisateurs'" class="shadow" />
  <div class="d-flex justify-content-center align-items-center py-md-3" style="height: 90vh">
    <div class="container p-0">
      <button class="btn btn-secondary back rounded-5 mt-1 position-absolute mx-2 bg-sm-transparent z-1"
        style="width: 40px; height: 40px" @click="$router.back" title="Retourner">
        <fai icon="arrow-left" />
      </button>
      <div class="bg-white rounded-4 shadow opacity-75 d-flex align-items-center justify-content-center">
        <div>
          <form @submit.prevent="submitForm()" class="row p-4 m-auto">
            <h1 class="text-center text-primary">
              {{ action }} un utilisateur
            </h1>
            <div class="container mt-3">
              <div class="row row-cols-1">
                <div class="mb-3 col">
                  <label for="exampleInputName" class="form-label">Nom de l'utilisateur</label>
                  <input type="text" class="form-control" id="exampleInputName" v-model="formData.name" minlength="3"
                    required />
                </div>
                <div class="mb-3 col">
                  <label for="exampleInputEmail1" class="form-label">Email</label>
                  <input type="email" class="form-control" id="exampleInputEmail1" v-model="formData.email" required />
                </div>
              </div>
              <div class="mb-4 row">
                <div class="col">
                  <label for="roleList" class="form-label mt-4">Rôle</label>
                  <select class="form-select" id="roleList" v-model="formData.role" required>
                    <option value="">----Choisir le rôle----</option>
                    <option v-for="(role, index) in roles" :key="index" :value="role.roleTitle">
                      {{ role.roleTitle }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="err-box w-50 w-sm-100 rounded-4 p-1 m-auto d-flex" v-if="error.length">
                <p class="text-center w-100 mt-3 fs-6 pt-2">
                  {{ error }}
                </p>
                <button type="button" class="btn-close ms-auto p-2 position-absolute" @click="error = ''"></button>
              </div>
              <div class="succ-box w-50 w-sm-100 rounded-4 p-1 m-auto d-flex" v-if="success.length">
                <p class="text-center w-100 mt-3 fs-6 pt-2">
                  {{ success }}
                </p>
                <button type="button" class="btn-close ms-auto p-2 position-absolute" @click="error = ''"></button>
              </div>
              <div class="row">
                <button type="submit" class="btn btn-primary btns" :disabled="submited ? true : false">
                  <span v-if="!submited"> Valider </span>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Patientez
                  </span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter, useRoute } from 'vue-router'
import { onMounted, ref } from 'vue'
import apiClient from '@/api'
import NavBar from '../navBar.vue'

const formData = ref({
  name: '',
  role: '',
  email: '',
})
const success = ref('')
const router = useRouter()
const route = useRoute()
const action = route.params.action
const error = ref('')
const roles = ref([])
const submited = ref(false)
const user = ref([])
let url1 = ref()
const url2 = '/roles/get'
let errorTimeOut
let successTimeOut

const setError = (errorText) => {
  error.value = errorText
  clearTimeout(errorTimeOut)
  errorTimeOut = setTimeout(() => {
    error.value = ''
  }, 5000)
}

const setSuccess = (successText) => {
  success.value = successText
  clearTimeout(successTimeOut)
  successTimeOut = setTimeout(() => {
    success.value = ''
  }, 5000)
}

const getRole = async () => {
  try {
    const response = await apiClient.get(url2)
    if (response.data.code !== 200) {
      console.log(response.data.message)
      let e = 'Une erreur est survenue lors du chargement des rôles'
      setError(e)
    } else {
      roles.value = response.data.roles
    }
  } catch (e) {
    let er = e.data ? e.response.data.message : 'Impossible d\'etablir une connexion avec le serveur'
    setError(e)
  }
}

const checkAction = () => {
  if (action === 'Ajouter') {
    url1.value = '/users/add'
  } else if (action === 'Editer') {
    user.value = JSON.parse(route.query.user)
    url1.value = `/users/${user.value.userID}/edit`
    formData.value.email = user.value.email
    formData.value.name = user.value.name
    formData.value.role = user.value.role.roleTitle
  } else {
    router.back()
  }
}

const submitForm = async () => {
  submited.value = true
  const form = new FormData()
  form.append('name', formData.value.name)
  form.append('email', formData.value.email)
  form.append('role', formData.value.role)
  try {
    const response = await apiClient.post(url1.value, form)
    if (response.data.code === 200) {
      let succ = response.data.message
      setSuccess(succ)
    } else {
      let e = response.data.message
      setError(e)
    }
  } catch (e) {
    let er = e.response ? e.response.data.message : 'Impossible d\'etablir une connexion avec le serveur'
    success.value = ''
    setError(er)
    console.error(e)
  } finally {
    submited.value = false
  }
}

onMounted(() => {
  getRole()
  checkAction()
})
</script>

<style>
.btn {
  width: 100%;
}

.alert {
  top: 20px;
  right: 20px;
  z-index: 1050;
}
</style>
