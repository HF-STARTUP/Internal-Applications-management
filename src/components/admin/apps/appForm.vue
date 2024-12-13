<template>
  <navBar :apps="true" :title="'applications'" class="shadow" />
  <div class="d-flex justify-content-center align-items-center py-md-3" style="height: 80vh">
    <div class="m-3 container m-auto bg-white opacity-75 rounded-4 p-4 shadow">
      <button class="btn btn-secondary back rounded-5 position-absolute bg-sm-transparent z-1" style="width: 40px"
        title="Retourner" @click.prevent="$router.back()">
        <fai icon="arrow-left"></fai>
      </button>
      <h1 class="text-center text-primary mt-5">
        {{ route.params.action }} une application
      </h1>
      <div class="col-md-6 m-auto position-relative">
        <div class="mt-3">
          <form @submit.prevent="submitForm">
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Nom de l'application
              </label>
              <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                v-model="appName" required />
            </div>
            <div class="">
              <label for="exampleInputPassword1" class="form-label">Lien de l'application</label>
              <input type="link" class="form-control" id="exampleInputPassword1" v-model="appLink" required />
            </div>
            <div class="mb-3">
              <label for="formFile" class="form-label mt-4 d-flex align-items-center w-100">
                <button @click.prevent="$refs.fileInput.click()" class="btn btn-dark rounded-5 p-2 fs-4 me-3"
                  style="width: 50px; height: 50px">
                  <fai icon="camera" />
                </button>
                <p>
                  Icone de l'application <br />
                  <span class="text-warning"> (*.png, *.jpg,*.jpeg) </span>
                  <br />
                  <span v-if="appImage">
                    {{ appImage.name }}
                  </span>
                  <span v-if="fileError.length" class="text-danger">
                    {{ fileError }}
                  </span>
                </p>
              </label>
              <input class="form-control d-none" type="file" id="formFile" ref="fileInput"
                v-on:change="appImage = onFileChange()" accept=".jpg,.png,.jpeg" />
              <span v-if="$route.params.action == 'Editer'" class="text-danger">Ne choisir un fichier que si vous
                souhaitez modifier l'icone
                ci-dessous</span>
              <div v-if="$route.params.action == 'Editer'" class="row-cols-5 m-auto text-center">
                <img :src="backendServer + app.appImage" alt="" class="img img-fluid" />
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
              <button type="button" class="btn-close ms-auto p-2 position-absolute" @click="success = ''"></button>
            </div>
            <button type="submit" class="btn btn-primary btns" :disabled="submited ? true : false">
              <span v-if="!submited"> Valider </span>
              <span v-else>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Patientez
              </span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import navBar from '../navBar.vue'
import apiClient from '@/api'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth';

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const backendServer = authStore.backendServer
const appImage = ref(null)
const appName = ref('')
const appLink = ref('')
const app = ref([])
const appID = route.query.appID ? route.query.appID : ''
const url = ref('')
const error = ref('')
const fileError = ref('')
const success = ref('')
let errorTimeOut
let fileErrorTimeOut
let successTimeOut

const fileInput = ref(null)
const submited = ref(false)

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

const setFileError = (errorText) => {
  fileError.value = errorText
  clearTimeout(fileErrorTimeOut)
  fileErrorTimeOut = setTimeout(() => {
    fileError.value = ''
  }, 5000)
}
const onFileChange = () => {
  const file = fileInput.value ? fileInput.value.files[0] : null

  if (file) {
    const validTypes = ['image/png', 'image/jpg', 'image/jpeg']
    if (!validTypes.includes(file.type)) {
      let e = "L'image sélectionnée n\'est pas valide"
      setFileError(e)
      return null
    } else {
      return file
    }
  }
  return null
}

if (route.params.action === 'Ajouter') {
  url.value = '/apps/add'
} else if (route.params.action === 'Editer') {
  url.value = `/apps/${route.query.appID}/edit`
  console.log(url.value)
} else {
  router.back()
}

const getAppById = async () => {
  if (route.params.action === 'Editer') {
    let url = `/apps/get/${appID}`
    try {
      await apiClient.get(url).then(response => {
        if (response.data.code == 200) {
          app.value = response.data.app
          appName.value = app.value.appName
          appLink.value = app.value.appLink
        } else {
          setError(response.data.message)
          console.error(response.data.message)
        }
      })
    } catch (error) {
      console.error(error)
    }
  }
}
const submitForm = async () => {
  submited.value = true
  appImage.value = onFileChange()
  let formData = new FormData()
  if (appImage.value) {
    formData.append('appImage', appImage.value)
  }
  formData.append('appName', appName.value)
  formData.append('appLink', appLink.value)

  try {
    const response = await apiClient.post(url.value, formData)
    if (response.data.code === 200) {
      let e = response.data.message
      setSuccess(e)
    } else {
      let e = response.data.message
      setError(e)
    }
  } catch (er) {
    let e = er.response ? er.response.data.message : 'Impossible d\'etablir une connexion avec le serveur'
    setError(e)
    console.error(error)
  } finally {
    submited.value = false
  }
}
onMounted(() => {
  getAppById()
})
</script>
