<template>
  <div class="container container-fluid footer text-white bottom-0 w-100 btn-rh p-2 d-flex align-items-center">
    <div class="row">
      <div class="footer-content">
        <img src="https://rhopenlabs.africa/wp-content/uploads/2022/09/logo-removebg-preview-2.png" alt="">
        <div class="footer-links">
          <a href="https://rhopenlabs.africa/a-propos/" class="footer-link" target="_blank">À propos</a>
          <a href="/faq" class="footer-link">FAQ</a>
        </div>
        <div class="footer-copy">
          © 2024 RHOPEN LABS. Tous droits réservés.
        </div>
      </div>
    </div>
    <div class="address">
      <div class="location">
        Douala, Kotto, BP 5979
      </div>
      <div class="email">
        contact@rhopenlabs.africa
      </div>
      <div class="tel">
        +237 233 47 22 39
      </div>
    </div>
    <div class="avis" style="max-width: 250px;">
      <form @submit.prevent="submitPOV" class="w-100 d-block">
        <label for="email" class="m-auto text-center">
          Donner un avis
        </label>
        <input type="email" class="form-control" placeholder="Votre email" v-model="userEmail" required>
        <textarea class="form-control" placeholder="Entrer du texte" id="POV" v-model="userPOV" required />
        <button type="submit" class="btn btn-secondary" desabled="true">Envoyer</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import apiClient from '@/api';
import { ref } from 'vue';

const userEmail = ref('');
const userPOV = ref('');
const error = ref('');
const success = ref('');
var setTimeoutFetchMsg;
const submitPOV = async () => {
  if (!userPOV.value.length) {
    success.value = '';
    error.value = 'Veuillez renseigner votre avis';
    document.getElementById('POV').focus();
    return
  } else {
    const formdata = new FormData();
    formdata.append('userEmail', userEmail.value);
    formdata.append('avis', userPOV.value);
    const url = '/avis';
    await apiClient.post(url, formdata).then((response) => {
      if (response.data.code == 200) {
        error.value = '';
        success.value = 'Votre avis a ete soumis';
        clearTimeout(setTimeoutFetchMsg);
        setTimeoutFetchMsg = setTimeout(() => {
          success.value = '';
        }, 3000);
      } else {
        success.value = '';
        error.value = response.data.message;
        clearTimeout(setTimeoutFetchMsg);
        setTimeoutFetchMsg = setTimeout(() => {
          error.value = '';
        }, 3000);
      }
    }).catch((e) => {
      if (e.data) {
        success.value = '';
        error.value = e.response.data.message;
        clearTimeout(setTimeoutFetchMsg);
        setTimeoutFetchMsg = setTimeout(() => {
          error.value = '';
        }, 3000);
      } else {
        success.value = '';
        error.value = 'Probleme de connexion au serveur. Veuillez verifier votre connexion internet';
        clearTimeout(setTimeoutFetchMsg);
        setTimeoutFetchMsg = setTimeout(() => {
          error.value = '';
        }, 3000);
      }
    })
  }
}
</script>
