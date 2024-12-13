<template>
  <div class="container container-fluid w-100 d-flex align-items-center justify-content-center" style="height: 100vh">
    <div class="m-3 col-md-6 container bg-white px-5 py-2 shadow rounded-4 opacity-75 mt-2">
      <div class="m-auto">
        <img src="../../assets/LOGO-RHOPEN-LABS.png" alt="RH_OpenApps"
          class="img m-auto rh-logo-login img-fluid mb-5" />
        <h1 class="text-left title text-shadow text-primary text-center fw-bolder w-100">
          Connectez-vous
        </h1>
        <div class="mt-3">
          <form @submit.prevent="submitForm">
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Email </label>
              <input type="email" class="form-control" id="exampleInputEmail1" v-model="form.email" required />
            </div>
            <div class="mb-3">
              <label for="exampleInputPassword1" class="form-label">Mot de passe</label>
              <input type="password" class="form-control" id="exampleInputPassword1" v-model="form.pwd" required />
            </div>
            <div class="err-box w-50 w-sm-100 rounded-4 p-1 m-auto d-flex" v-if="error.length">
              <p class="text-center w-100 mt-3 fs-6 pt-2">
                {{ error }}
              </p>
              <button type="button" class="btn-close ms-auto p-2 position-absolute" @click="error = ''"></button>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="exampleCheck1" v-model="isChecked" />
              <label class="form-check-label" for="exampleCheck1">Rester connecter sur cet
                appareil</label>
            </div>
            <div class="form-link">
              <p>
                <router-link to="/resetPassword" class="nav-link cursor-pointer text-rh text-center reset-pwd-link">J'ai
                  oublie mon mot de
                  passe</router-link>
              </p>
            </div>
            <button type="submit" class="btn btn-primary btns" :disabled="isConnecting ? true : false">
              <span v-if="!isConnecting"> Se connecter </span>
              <span v-else>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Connexion
              </span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const error = ref('')
let errorTimeOut
const isChecked = ref(false)
const form = ref({
  email: '',
  pwd: '',
})

const isConnecting = ref(false)

const submitForm = async () => {
  isConnecting.value = true
  const url = `${authStore.apiServer}users/login`
  const formData = new FormData()

  formData.append('email', form.value.email)
  formData.append('password', form.value.pwd)

  try {
    const response = await axios.post(url, formData)
    if (response.data.code === 200) {
      authStore.setToken(response.data.token, isChecked.value)
      authStore.setUser(response.data.user)
    } else {
      error.value = response.data.message
      clearTimeout(errorTimeOut)
      errorTimeOut = setTimeout(() => {
        error.value = '';
      }, 5000);
    }
  } catch (e) {
    if (e.data) {
      error.value = error.response.data.message
      clearTimeout(errorTimeOut)
      errorTimeOut = setTimeout(() => {
        error.value = '';
      }, 5000);
    } else {
      error.value = 'Probleme de connexion au serveur'
      clearTimeout(errorTimeOut)
      errorTimeOut = setTimeout(() => {
        error.value = '';
      }, 5000);
    }
  } finally {
    isConnecting.value = false
    router.push('/applications')
  }
}
</script>

<style>
.btn {
  width: 100%;
}

.title {
  animation: atterirLeger 0.8s ease-out;
}

.rh-logo-login {
  animation: atterirLogo 1.5s ease-in-out;
  transition: all 2s ease-in-out;
}

@keyframes atterirLogo {
  0% {
    transform: translateY(-350px);
    scale: 0.1;
    opacity: 0;
  }

  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes atterirLeger {
  0% {
    transform: translateY(-20px);
    scale: 0.1;
    opacity: 0;
  }

  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

.btns:hover {
  background-color: rgba(0, 0, 250, 0.715) !important;
  transition: all 1s ease-in-out;
}

.reset-pwd-link:hover {
  text-decoration: underline;
  transform: scale(1.2);
  transition: all .5s ease-in-out;
}
</style>
