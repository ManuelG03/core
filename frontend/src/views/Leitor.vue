<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const selectedFile = ref<any>(null);
const extractedText = ref('');
const isLoading = ref(false);

// Função para enviar o ficheiro PDF para o backend
const enviarPDFbackend = async () => {
  let file = selectedFile.value;
  
  if (Array.isArray(file) && file.length > 0) {
    file = file[0];
  }
  
  if (!file) {
    alert('Por favor, selecione um ficheiro PDF antes de enviar.');
    return;
  }

  isLoading.value = true;
  try {
    const formData = new FormData();
    formData.append('pdf', file);

    const response = await axios.post('http://localhost:8000/enviarpdf', formData);

  console.log('Resposta do backend:', response.data);
    
    if (response.data.text) {
      extractedText.value = response.data.info;
      //console.log('Texto extraído:', extractedText.value);
    } else {
      console.error('Nenhum campo text na resposta:', response.data);
      alert('Erro: Nenhum texto foi extraído');
    }
  } catch (error: any) {
    console.error('Erro ao enviar ficheiro:', error);
    console.error('Resposta de erro:', error.response?.data);
    alert('Erro ao processar ficheiro PDF: ' + (error.response?.data?.message || error.message));
  } finally {
    isLoading.value = false;
  }
};

  // Função para exportar o texto extraído para um ficheiro Excel.

const exportarParaExcel = async () => {
  if (!extractedText.value) {
    alert('Nenhum texto para exportar');
    return;
  }

  try {
    const response = await axios.post('http://localhost:8000/exportar', { text: extractedText.value }, { responseType: 'blob' });

    // Criar um link para download do ficheiro Excel que foi gerado pelo backend.
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'extracto_remuneraçoes.xlsx');
    document.body.appendChild(link);
    link.click();
    link.parentNode?.removeChild(link);
  } catch (error: any) {

    if (error.response?.data instanceof Blob) {
      try {
        const text = await error.response.data.text();
        console.error('Erro do servidor:', text);
        alert('Erro ao exportar: ' + text);
      } catch (e) {
        alert('Erro ao exportar: ver consola para detalhes');
      }
    } else {
      alert('Erro ao exportar: ' + (error.response?.data?.message || error.message));
    }
  }
};

</script>

// Página para inserir ficheiros
<template>
  <v-app>
    <v-main>
        <v-container>
            <h1>Leitor de Ficheiros PDF</h1>
            <p>Aqui poderá carregar e ler ficheiros PDF.</p>

            <v-file-input 
              v-model="selectedFile"
              label="Carregar Ficheiro PDF" 
              accept=".pdf" 
              variant="underlined" 
              :disabled="isLoading"
            ></v-file-input>

            <div v-if="selectedFile" style="margin-top: 25px;">
              <v-btn color="primary" @click="enviarPDFbackend" :disabled="isLoading">
                Processar PDF
              </v-btn>
            </div>

            <v-progress-linear v-if="isLoading" indeterminate></v-progress-linear>

            <v-textarea
              v-if="extractedText"
              v-model="extractedText"
              label="Texto Extraído"
              variant="outlined"
              rows="10"
              style="margin-top: 40px;"
            ></v-textarea>

            <div v-if="extractedText" style="margin-top: 25px;">
              <v-btn color="success" @click="exportarParaExcel">Exportar para Excel</v-btn>
            </div>
        </v-container>
    </v-main>
  </v-app>
</template>

