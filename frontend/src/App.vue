<script setup lang="ts">
import { ref } from 'vue'

const drawer = ref(false)
</script>

<template>
  <v-app>
    <v-app-bar color="primary">
      <v-app-bar-nav-icon @click="drawer = !drawer" style="color: white;"></v-app-bar-nav-icon>
      <v-app-bar-title>Aplicação</v-app-bar-title>
      <v-spacer></v-spacer>
      <v-btn variant="text" to="/">Home</v-btn>
      <v-btn variant="text" to="/leitor">Leitor</v-btn>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<style scoped>
#app {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  font-family: Arial, sans-serif;
}

h1 {
  color: #333;
  text-align: center;
}

ul {
  list-style: none;
  padding: 0;
}

.user-item {
  padding: 0;
  margin: 8px 0;
  background-color: #f0f0f0;
  border-radius: 4px;
  overflow: hidden;
}

.user-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
}

.dropdown {
  position: relative;
}

.dropdown-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  padding: 5px 10px;
}

.dropdown-menu {
  position: absolute;
  right: 0;
  top: 100%;
  background: white;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  z-index: 10;
  min-width: 120px;
}

.dropdown-menu button {
  display: block;
  width: 100%;
  padding: 10px;
  border: none;
  background: none;
  text-align: left;
  cursor: pointer;
  font-size: 14px;
}

.dropdown-menu button:hover {
  background-color: #f5f5f5;
}

.posts-section {
  margin-top: 30px;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 4px;
}

.posts-section h2 {
  color: #333;
}

.post-item {
  padding: 10px;
  margin: 8px 0;
  background-color: white;
  border-left: 3px solid #42b983;
  border-radius: 2px;
}

.loading,
.error,
.empty {
  text-align: center;
  padding: 20px;
  font-size: 16px;
}

.error {
  color: #d32f2f;
  background-color: #ffebee;
  border-radius: 4px;
}

.empty {
  color: #999;
}
</style>
