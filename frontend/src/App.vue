<script setup lang="ts">
import { ref } from 'vue'

const username = ref('')
const users = ref<string[]>([])

const addUser = () => {
  if (username.value.trim()) {
    users.value.push(username.value)
    username.value = ''
  }
}

const handleKeypress = (e: KeyboardEvent) => {
  if (e.key === 'Enter') {
    addUser()
  }
}
</script>

<template>
  <div class="container">
    <h1>Gestão de Utilizadores</h1>
    
    <div class="input-group">
      <input 
        v-model="username" 
        type="text" 
        placeholder="Insira o nome de utilizador"
        @keypress="handleKeypress"
      />
      <button @click="addUser">Inserir</button>
    </div>

    <div v-if="users.length > 0" class="users-list">
      <h2>Utilizadores Adicionados:</h2>
      <ul>
        <li v-for="(user, index) in users" :key="index">{{ user }}</li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  font-family: Arial, sans-serif;
}

h1 {
  color: #333;
  text-align: center;
}

.input-group {
  display: flex;
  gap: 10px;
  margin: 20px 0;
}

input {
  flex: 1;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

input:focus {
  outline: none;
  border-color: #42b983;
}

button {
  padding: 10px 20px;
  font-size: 16px;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #369970;
}

.users-list {
  margin-top: 30px;
}

.users-list h2 {
  color: #333;
}

.users-list ul {
  list-style: none;
  padding: 0;
}

.users-list li {
  padding: 10px;
  margin: 5px 0;
  background-color: #f0f0f0;
  border-radius: 4px;
}
</style>
