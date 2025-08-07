<template>
  <v-container>
    <v-card style="width: 100%;">
      <!-- <v-card-title>Lista pojazdów</v-card-title> -->

      <v-data-table
        :headers="headers"
        :items="vehicles"
        :items-per-page="5"
        :search="search"
        class="elevation-1"
        :loading="loading"
      >
        <template v-slot:top>
            <div class="d-flex">

                <div class="px-4 py-2">
                    <v-btn icon>
                        <v-icon color="green">
                            mdi-plus
                        </v-icon>
                    </v-btn>
            </div>
            <v-spacer />
            <div class="px-4 py-2">
                <v-text-field
                v-model="search"
                label="Szukaj"
                dense
                hide-details
                clearable
                style="max-width: 200px;"
                />
            </div>
            </div>
        </template>

        <template v-slot:item.createdAt="{ item }">
            <BodyCellDateTime :value="item.createdAt" />
        </template>

        <template v-slot:item.updatedAt="{ item }">
            <BodyCellDateTime :value="item.updatedAt" />
        </template>

        <template v-slot:item.actions="{ item }">
            <v-btn icon @click="editVehicle(item.id)">
                <v-icon color="primary">mdi-pencil</v-icon>
            </v-btn>
            <v-btn icon @click="deleteVehicle(item.id)">
                <v-icon color="red">mdi-delete</v-icon>
            </v-btn>
        </template>
      </v-data-table>
      <v-btn @click="notify">Show Toast</v-btn>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import BodyCellDateTime from './BodyCellDateTime.vue'
import { useToast } from '../composables/useToast'
const $toast = useToast()
interface Vehicle {
  id: string
  registrationNumber: string
  brand: string
  model: string
  type: string
  createdAt: string
  updatedAt: string
}

const vehicles = ref<Vehicle[]>([])
const search = ref('')
const fetchLoading = ref(false)
const loading = computed(() => {
    return fetchLoading.value
})
const headers = [
  { text: 'ID', value: 'id' },
  { text: 'Rejestracja', value: 'registrationNumber' },
  { text: 'Marka', value: 'brand' },
  { text: 'Model', value: 'model' },
  { text: 'Typ', value: 'type' },
  { text: 'Utworzono', value: 'createdAt' },
  { text: 'Zaktualizowano', value: 'updatedAt' },
  { text: 'Akcje', value: 'actions', sortable: false}
]
const notify = () => {
      $toast.info('Hello from toast!')
    }
const fetchData = () => {
    fetchLoading.value = true
    axios.get<{ results: Vehicle[] }>('http://localhost:8008/vehicles').then((response) => {
        vehicles.value = response.data.results
        fetchLoading.value = false
    }).catch((error) => {
        fetchLoading.value = false
        console.log(error)
    })
}

const deleteVehicle = (id: string) => {
    axios.delete('http://localhost:8008/vehicles/delete/1').then((response) => {
        console.log(response, 'a')
    }).catch((error) => {
        console.log(error)
    })
}

const editVehicle = (id: string) => {

}

onMounted( () => {
    fetchData()
})
</script>
