<template>
  <v-container>
    <v-card>
      <v-card-title>Lista pojazdów</v-card-title>

      <v-data-table
        :headers="headers"
        :items="vehicles"
        :items-per-page="5"
        class="elevation-1"
        dense
      >
        <template v-slot:item.createdAt="{ item }">
          {{ formatDate(item.createdAt) }}
        </template>
        <template v-slot:item.updatedAt="{ item }">
          {{ formatDate(item.updatedAt) }}
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface Vehicle {
  id: number
  registrationNumber: string
  brand: string
  model: string
  type: string
  createdAt: string
  updatedAt: string
}

const vehicles = ref<Vehicle[]>([])

const headers = [
  { text: 'ID', value: 'id' },
  { text: 'Rejestracja', value: 'registrationNumber' },
  { text: 'Marka', value: 'brand' },
  { text: 'Model', value: 'model' },
  { text: 'Typ', value: 'type' },
  { text: 'Utworzono', value: 'createdAt' },
  { text: 'Zaktualizowano', value: 'updatedAt' }
]
const fetchData = async () => {
    axios.get<{ results: Vehicle[] }>('http://localhost:8008/vehicles').then((response) => {
        vehicles.value = response.data.results
    }).catch((error) => {
        console.log(error)
    })
}

const formatDate = (dateStr: string): string => {
  return new Date(dateStr).toLocaleString()
}

onMounted( () => {
    fetchData()
})
</script>
