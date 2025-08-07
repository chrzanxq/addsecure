<template>
  <v-container>
    <v-card style="width: 100%;">
            <v-data-table
              v-model="selected"
              :headers="headers"
              :items="vehicles"
              :items-per-page="5"
              :search="search"
              item-value="id"
              class="elevation-1"
              :loading="loading"
              :item-class="getRowClass"
              :footer-props="{
                itemsPerPageOptions: [5, 10, 20, 50],
                showFirstLastPage: true,
                firstIcon: 'mdi-page-first',
                lastIcon: 'mdi-page-last'
              }"
              :custom-sort="sortFn"
              @click:row="handleRowClick"
            >

            <template v-slot:top>
                <div class="d-flex align-center px-4 py-2">
                    <v-btn icon color="green" @click="openAddDialog" class="mr-2">
                    <v-icon>mdi-plus</v-icon> 
                    </v-btn>

                  <v-btn
                    v-if="bulkMode"
                    color="red"
                    @click="deleteSelected"
                    :disabled="!selected.length"
                  >
                    <v-icon left>mdi-delete</v-icon> Delete Selected
                  </v-btn>


                    <v-spacer />
                    <v-text-field
                    v-model="search"
                    label="Search"
                    dense
                    hide-details
                    clearable
                    style="max-width: 200px;"
                    />
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
    </v-card>

    <v-dialog v-model="dialog" max-width="500px">
        <v-card>
            <v-card-title>{{ isEditing ? 'Edit Vehicle' : 'Add Vehicle' }}</v-card-title>
            <v-card-text>
            <v-text-field v-model="form.registrationNumber" label="Registration" />
            <v-text-field v-model="form.brand" label="Brand" />
            <v-text-field v-model="form.model" label="Model" />
            <v-text-field v-model="form.type" label="Type" />
            </v-card-text>
            <v-card-actions>
            <v-spacer />
            <v-btn text @click="dialog = false">Cancel</v-btn>
            <v-btn color="primary" @click="saveVehicle">Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, onBeforeUnmount, nextTick } from 'vue'
import BodyCellDateTime from './BodyCellDateTime.vue'
import { useToast } from '../composables/useToast'
import $api from '../helpers/ApiRest'

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

//should be env
const apiBaseUrl = ref('')

const vehicles = ref<Vehicle[]>([])
const selected = ref<string[]>([])
const isCtrlDown = ref(false)
const search = ref('')
const fetchLoading = ref(false)
const loading = computed(() => fetchLoading.value)

const dialog = ref(false)
const isEditing = ref(false)
const bulkMode = ref(false)
const editingId = ref<string | null>(null)

const form = ref({
  registrationNumber: '',
  brand: '',
  model: '',
  type: '',
})




const headers = [
  { text: 'ID', value: 'id' },
  { text: 'Rejestracja', value: 'registrationNumber' },
  { text: 'Marka', value: 'brand' },
  { text: 'Model', value: 'model' },
  { text: 'Typ', value: 'type' },
  { text: 'Utworzono', value: 'createdAt' },
  { text: 'Zaktualizowano', value: 'updatedAt' },
  { text: 'Akcje', value: 'actions', sortable: false },
]

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.ctrlKey || e.metaKey) isCtrlDown.value = true
}

const handleKeyUp = (e: KeyboardEvent) => {
  isCtrlDown.value = false
}

const handleRowClick = (props: any) => {
  if (!isCtrlDown.value) return
  console.log(props)
  const id = props?.id
  const index = selected.value.indexOf(id)
  console.log(index)
  if (index === -1) {
    selected.value.push(id)
  } else {
    selected.value.splice(index, 1)
  }

  nextTick(() => {
    bulkMode.value = selected.value.length > 0
  })
}

const getRowClass = (item: Vehicle) => {
  return selected.value.includes(item.id) ? 'selected-row' : ''
}


function sortFn(items: any[], sortBy: string[], sortDesc: boolean[]) {
  return items.sort((a, b) => {
    const key = sortBy[0]
    const desc = sortDesc[0]

    const getComparableValue = (item: any) => {
      const value = item[key]

      // Check if it's a date object
      if (value && value.date && typeof value.date === 'string') {
        return new Date(value.date).getTime()
      }

      // Fallback for string/number
      return typeof value === 'string' || typeof value === 'number' ? value : ''
    }

    const aVal = getComparableValue(a)
    const bVal = getComparableValue(b)

    if (aVal < bVal) return desc ? 1 : -1
    if (aVal > bVal) return desc ? -1 : 1
    return 0
  })
}


// Fetch data
const fetchData = () => {
  fetchLoading.value = true
  $api
    .get('/vehicles')
    .then((response) => {
      vehicles.value = response.data.results
    })
    .catch((error) => {
      console.error(error)
    })
    .finally(() => {
      fetchLoading.value = false
    })
}

// Delete single
const deleteVehicle = (id: string) => {
  $api
    .delete(`/vehicles/delete/${id}`)
    .then(() => {
      $toast.success('Deleted')
      fetchData()
    })
    .catch(() => {
      $toast.error('Error deleting')
    })
}

// Bulk delete
const deleteSelected = () => {
  const ids = selected.value
  Promise.all(
    ids.map((id) => $api.delete(`/vehicles/delete/${id}`))
  )
    .then(() => {
      $toast.success(`Deleted ${ids.length} vehicle(s)`)
      selected.value = []
      fetchData()
    })
    .catch(() => {
      $toast.error('Error deleting selected vehicles')
    })
}

// Open add dialog
const openAddDialog = () => {
  isEditing.value = false
  editingId.value = null
  form.value = {
    registrationNumber: '',
    brand: '',
    model: '',
    type: '',
  }
  dialog.value = true
}

// Open edit dialog
const editVehicle = (id: string) => {
  const vehicle = vehicles.value.find((v) => v.id === id)
  if (!vehicle) return

  isEditing.value = true
  editingId.value = id
  form.value = {
    registrationNumber: vehicle.registrationNumber,
    brand: vehicle.brand,
    model: vehicle.model,
    type: vehicle.type,
  }
  dialog.value = true
}

// Save (create or update) (id = 0 is an ugly workaround for having single POST endpoint)
const saveVehicle = () => {
  const url = isEditing.value && editingId.value
    ? `/vehicles/save/${editingId.value}`
    : '/vehicles/save/0'

  $api
    .post(url, form.value)
    .then(() => {
      $toast.success(isEditing.value ? 'Vehicle updated' : 'Vehicle added')
      dialog.value = false
      fetchData()
    })
    .catch(() => {
      $toast.error('Error saving vehicle')
    })
}

onMounted(() => {
  fetchData()
  window.addEventListener('keydown', handleKeyDown)
  window.addEventListener('keyup', handleKeyUp)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeyDown)
  window.removeEventListener('keyup', handleKeyUp)
})
</script>

<style scoped>
::v-deep(.selected-row) {
  background-color: #fde3fd !important; 
}
</style>

