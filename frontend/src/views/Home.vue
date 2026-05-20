<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const utilizadores = ref<any[]>([])
const posts = ref<any[]>([])
const loading = ref(true)
const error = ref('')
const openDropdown = ref<number | null>(null)
const UtilizadorPosts = ref<any[]>([])

onMounted(async () => {
  try {
    const response = await axios.get('http://localhost:8000/utilizadores')
    utilizadores.value = response.data
    error.value = ''
  } catch (err: any) {
    error.value = err.message || 'Erro ao carregar utilizadores'
    console.error(err)
  } finally {
    loading.value = false
  }
})

const viewPosts = async (userId: number) => {
  try {
    const response = await axios.get(`http://localhost:8000/posts/${userId}`)
    UtilizadorPosts.value = response.data
    console.log(`Posts do utilizador ${userId}:`, response.data)
  } catch (err: any) {
    console.error(`Erro ao carregar posts do utilizador ${userId}:`, err.message)
  }
  openDropdown.value = null
}
</script>

<template>
  <v-container>
    <h1>Lista de Utilizadores</h1>
    
    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="utilizadores.length > 0">
      <ul>
        <li v-for="utilizador in utilizadores" :key="utilizador.id" class="user-item">
          <div class="user-info">
            <span>{{ utilizador.name }} (@{{ utilizador.username }})</span>
            <div>
                <button @click="viewPosts(utilizador.id)">Ver Posts</button>
            </div>
          </div>
        </li>
      </ul>

      
      <div v-if="UtilizadorPosts.length > 0" class="posts-section">
        <h2>Posts do Utilizador:</h2>
        <ul>
          <li v-for="post in UtilizadorPosts" :key="post.id" class="post-item">
            {{ post.content }}
          </li>
        </ul>
      </div>
      <div v-else-if="UtilizadorPosts.length === 0"><h2>Sem posts para este utilizador</h2></div>
    </div>
    <div v-else class="empty">Sem utilizadores</div>
  </v-container>
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

.post-item {
  padding: 10px;
  margin: 4px 0;
  background-color: #fff;
  border-left: 3px solid #007bff;
}

.loading, .error, .empty {
  text-align: center;
  padding: 20px;
  font-size: 16px;
}

.error {
  color: red;
}

.empty {
  color: #999;
}
</style>
