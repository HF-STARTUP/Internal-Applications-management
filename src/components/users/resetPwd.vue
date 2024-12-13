<template>
  <div class="container container-fluid w-100 d-flex align-items-center justify-content-center" style="height: 100vh">
    <div class="m-3 col-md-6 container bg-white px-5 py-2 shadow rounded-4 opacity-75 mt-2">
      <div class="m-auto">
        <img src="../../assets/LOGO-RHOPEN-LABS.png" alt="RH_OpenApps"
          class="img m-auto rh-logo-login img-fluid mb-5" />
        <h1 class="title text-shadow fs-4 text-rh text-center fw-bolder w-100">
          Reinitialisation du mot de passe
        </h1>
        <div class="mt-3">
          <form @submit.prevent="submitForm()">
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Email </label>
              <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Entrer votre adresse email"
                v-model="email" required />
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
              <router-link to="/connexion" class="text-rh ">
                Se Connecter
              </router-link>
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
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import { ref } from 'vue';

const email = ref('');
const error = ref('');
const isConnecting = ref(false);
const success = ref('');
const authStore = useAuthStore();
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
  await axios.get(`${authStore.apiServer}resetPwd/${email.value}`).then((response) => {
    if (response.data.code == 200) {
      let succ = `Nous avons envoyer un nouveau mot de passe a l'adresse ${email.value}. utilisez le pour vous connecter`;
      setSuccess(succ)
    } else {
      let e = response.data.message;
      setError(e)
    }
  }).catch((e) => {
    let er = e.response ? e.response.data.message : 'Impossible d\'etablir une connexion avec le serveur'
    setError(er)
    console.error(e)
  }).finally(() => {
    isConnecting.value = false;
  })
}

</script>
