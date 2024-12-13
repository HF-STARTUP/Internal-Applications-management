<script setup>
import "vue3-carousel/dist/carousel.css";
import { Carousel, Slide, Navigation, Pagination } from "vue3-carousel";
import { ref } from "vue";

const config = {
  autoplay: 3000,
  wrapAround: true,
  pauseAutoplayOnHover: true,
  slides: 1,
  transition: 1000,
  easing: "ease-in-out",
};

const slides = [
  "Explorez une nouvelle façon d'accéder aux applications !",
  "Améliorez votre productivité avec des outils innovants.",
  "Découvrez une interface conçue pour vous.",
  "Passez au niveau supérieur avec notre plateforme.",
];

const isSession = localStorage.getItem('current_token');
</script>

<template>
  <div class="container p-0 w-100">
    <div class="hero-container">
      <div class="navbar w-100 px-4 position-fixed btn-rh shadow">
        <img src="https://rhopenlabs.africa/wp-content/uploads/2022/09/logo-removebg-preview-2.png"
          class="img img-fluid me-auto position-relative" alt="">
        <router-link :to="isSession ? '/applications' : '/connexion'" class="bg-purple btn-cta ms-auto">
          Je me connecte
        </router-link>

      </div>
      <div class="hero">
        <Carousel v-bind="config" class="carousel-container w-100">
          <Slide v-for="(text, index) in slides" :key="index" class="slide">
            <div class="slide-content">
              <h1 class="slide-title">{{ text }}</h1>
            </div>
          </Slide>
          <template #addons>
            <Navigation />
            <Pagination />
          </template>
        </Carousel>
      </div>
    </div>
    <main>
      <section class="context p-4">
        <div class="section-header">
          <div class="context-title text-secondary fs-6 fw-bold">
            <p class="fs-1">
              DE QUOI EST-IL QUESTION ?
            </p>
          </div>
          <div class="context-desciption text-rh fw-bold  fs-2">
            <p>
              Il s'agit d'une application web qui permet d'acceder de maniere
              securise et rapide a d'autres applications; celles
              heberges sur un VPN entre autres
            </p>
          </div>
          <img src="https://rhopenlabs.africa/wp-content/uploads/2022/09/Heading-Border1.png" alt="">
        </div>
        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-3 row-cols-lg-6 d-flex w-100 p-3 gap-2">
          <div class="col section-img container-fluid col-md-5">
            <img class="img cursor-pointer"
              src="../assets/DALL·E 2024-11-26 15.39.00 - A modern and minimalist flat-style illustration representing one-click access. The image shows a stylized hand or finger pointing towards a circular b.webp"
              alt="" height="80">

            <div class="desc container container-fluid">
              <p class="fs-5 fw-bolder text-rh">
                Accedez aux application en un seul clique
              </p>
              <p class="text-secondary fs-6 fw-bold">
                plus besoin de taper l'url complete dans la barre d'adresse
              </p>
            </div>
          </div>
          <div class="col section-img container container-fluid col-md-5">
            <img class="img cursor-pointer" src="../assets/Security-icon.png" alt="" height="80">
            <div class="desc container container-fluid">
              <p class="fs-5 fw-bolder text-rh">
                Controle des acces et de securite
              </p>
              <p class="text-secondary fs-6 fw-bold">
                C'est vous qui decidez quel utilisateur accede a une application en fonction de son role
              </p>
            </div>
          </div>
        </div>
      </section>
      <hr>
      <section class="context p-4">
        <div class="section-header">
          <div class="context-title text-secondary fs-6 fw-bold">
            <p class="fs-1">
              QUELS SONT SES AVANTAGES ?
            </p>
          </div>
          <div class="context-desciption text-rh fw-bold  fs-2">
            <p>
              Le but est de pouvoir faciliter l'utilisation des applications interne pour les utilisateurs
            </p>
          </div>
          <img src="https://rhopenlabs.africa/wp-content/uploads/2022/09/Heading-Border1.png" alt="">
        </div>
        <div class="row row-cols-1 row-cols-sm-1 row-cols-md-3 row-cols-lg-6 d-flex w-100 p-3 gap-2">
          <div class="col section-img container-fluid col-md-5">
            <img class="img cursor-pointer"
              src="../assets/Leonardo_Phoenix_A_highcontrast_black_and_white_illustration_d_1.jpg" alt="" height="80">

            <div class="desc container container-fluid">
              <p class="fs-5 fw-bolder text-rh">
                Centralisation des applications
              </p>
              <p class="text-secondary fs-6 fw-bold">
                Plus besoin de retenir des URL; toutes vos applications se trouve desormais au meme
                endroit!
              </p>
            </div>
          </div>
          <div class="col section-img container container-fluid col-md-5">
            <img class="img cursor-pointer"
              src="../assets/DALL·E 2024-11-26 15.39.00 - A modern and minimalist flat-style illustration representing one-click access. The image shows a stylized hand or finger pointing towards a circular b.webp"
              alt="" height="80">
            <div class="desc container container-fluid">
              <p class="fs-5 fw-bolder text-rh">
                Navigation simplifier
              </p>
              <p class="text-secondary fs-6 fw-bold">
                L'utilisateur peut naviger facilement d'une application a une autre.
              </p>
            </div>
          </div>
          <div class="col section-img container container-fluid col-md-5">
            <img class="img cursor-pointer"
              src="../assets/identity-colorful-line-icon-id-and-identification-vector-16051582.jpg" alt="" height="80">
            <div class="desc container container-fluid">
              <p class="fs-5 fw-bolder text-rh">
                Gestion des identifications
              </p>
              <p class="text-secondary fs-6 fw-bold">
                Une seule connexion suffit pour pouvoir acceder a
                plusieurs autre applications sans etre contraint de se reconnecter a chaque fois.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>
