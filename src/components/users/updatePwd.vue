<template>
  <navBar :title="'password'" v-if="store.user.is_admin" class="shadow" />
  <div class="container container-fluid w-100 d-flex align-items-center justify-content-center" style="height: 100vh">
    <div class="m-3 col-md-6 container bg-white px-5 py-2 shadow rounded-4 opacity-75 mt-2">
      <div class="m-auto py-5">
        <h1 class="title text-shadow text-rh text-center fw-bolder w-100 fs-4">
          Mise a jour du mot de passe
        </h1>
        <div class="mt-3">
          <form @submit.prevent="submitForm()">
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Nouveau mot de passe </label>
              <input type="password" class="form-control" id="inputpwd" v-model="password" required />
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Confirmer le mot de passe </label>
              <input type="password" class="form-control" id="inputpwdconfirm" v-model="password_confirmation"
                required />
            </div>
            <div class="succ-box w-50 w-sm-100 rounded-4 p-1 m-auto d-flex" v-if="success.length">
              <p class="text-center w-100 mt-3 fs-6 pt-2">
                {{ success }}
              </p>
              <button type="button" class="btn-close ms-auto p-2 position-absolute" @click="success = ''"></button>
            </div>
            <div class="err-box w-50 w-sm-100 rounded-4 p-1 m-auto d-flex" v-if="error.length">
              <p class="text-center w-100 mt-3 fs-6 pt-2">
                {{ error }}
              </p>
              <button type="button" class="btn-close ms-auto p-2 position-absolute" @click="error = ''"></button>
            </div>
            <p>
              <router-link to="/applications" class="text-rh ">
                << Applications </router-link>
            </p>
            <button type="submit" class="btn btn-primary btns" :disabled="isConnecting ? true : false">
              <span v-if="!isConnecting"> Envoyer </span>
              <span v-else>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Patientez...
              </span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import navBar from '../admin/navBar.vue';
import apiClient from '@/api';
import { useAuthStore } from '@/stores/auth';

const store = useAuthStore();
const password = ref('');
const password_confirmation = ref('');
const error = ref('');
const isConnecting = ref(false);
const success = ref('');
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

const submitForm = async () => {
  isConnecting.value = true
  const url = '/updatePassword';
  if (password.value === password_confirmation.value) {
    let formData = new FormData();
    formData.append('password', password.value);
    formData.append('password_confirmation', password_confirmation.value),
      await apiClient.post(url, formData).then((response) => {
        if (response.data.code == 200) {
          let succ = "Votre mot de passe a ete mis a jour !";
          setSuccess(succ)
        } else {
          let e = response.data.message;
          setError(e)
        }
      }).catch((e) => {
        let er = e.response ? e.response.data.message : 'Impossible d\'etablir une connexion avec le serveur';
        setError(er)
      }).finally(() => {
        isConnecting.value = false;
      })
  } else {
    isConnecting.value = false;
    let e = "les deux mots de passe ne correspondent pas !"
    setError(e)
  }
}
</script>
