<template>
  <VApp>
      <!-- see useToast.ts -->
      <notifications
        group="app"
        position="bottom center"
        animation-type="css"
        width="300"
        max="5"
        classes="vue-notification vuetify-toast"
      />
      <Table />
  </VApp>
</template>

  <script setup>
  import Table from './components/Table.vue'
  </script>