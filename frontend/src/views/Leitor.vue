<script setup lang="ts">


const handlefileupload = (file: File) => {
  if (file) {
    console.log('Ficheiro carregado:', file.name);
    fileReader.readAsText(file);
  }
};

const fileReader = new FileReader();
fileReader.onload = (event) => {
  const fileContent = event.target?.result;
  console.log('Conteúdo do ficheiro:', fileContent);
};

</script>

// Página para inserir ficheiros
<template>
  <v-app>
    <v-main>
        <v-container>
            <h1>Leitor de Ficheiros PDF</h1>
            <p>Aqui poderá carregar e ler ficheiros PDF.</p>

            <v-file-input label="Carregar Ficheiro PDF" accept=".pdf" variant="underlined" @change="handlefileupload"></v-file-input>

        </v-container>
    </v-main>
  </v-app>
</template>

