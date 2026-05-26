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

  // Função para exportar o texto extraído.

const exportar = async (formato: string = 'excel') => {
  if (!extractedText.value) {
    alert('Nenhum texto para exportar');
    return;
  }

  try {
   
    const response = await axios.post('http://localhost:8000/exportar',
    { text: extractedText.value,  formato },
    { responseType: formato === 'json' ? 'json' : 'blob' }
   );

    console.log('OPÇÃO DE EXPORTAÇÃO:', formato);
    if (formato === 'json') {
      const blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'extracto_remuneraçoes.json');
      document.body.appendChild(link);
      link.click();
      link.parentNode?.removeChild(link);
    } else {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'extracto_remuneraçoes.xlsx');
      document.body.appendChild(link);
      link.click();
      link.parentNode?.removeChild(link);
    }
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
              <v-btn color="success" @click="exportar('excel')">Exportar para Excel</v-btn>
            </div>

            <div v-if="extractedText" style="margin-top: 25px;">
              <v-btn color="deep-purple-lighten-1" @click="exportar('json')" >Gerar ficheiro JSON</v-btn>
            </div>

        </v-container>
    </v-main>
  </v-app>
</template>


