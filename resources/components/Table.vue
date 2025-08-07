<template>
  <v-container fluid>
    <v-card style="width: 100%;">
        <v-data-table
          v-model="selected"
          :headers="headers"
          :items="vehicles"
          :search="search"
          item-value="id"
          class="elevation-1"
          :options.sync="options"
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
                <!-- below is optional made as bonus -->
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
                  :label="$t('Search')"
                  dense
                  hide-details
                  clearable
                  style="max-width: 200px;"
                  />
              </div>
          </template>
          <template v-slot:item.lp="{ index }">
            <!-- persistent across pages, dont know if desired -->
            {{ getLpNumber(index) }}
          </template>
          <template v-slot:item.type="{ item }">
            <span class="text-capitalize">{{ $t(item.type) }}</span>
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
import { useTranslate } from '../composables/useTranslate'
import $api from '../helpers/ApiRest'

const $toast = useToast()
const { $t } = useTranslate()
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
const options = ref({
  page: 1,
  itemsPerPage: 5,
  sortBy: [],
  sortDesc: [],
  groupBy: [],
  groupDesc: [],
  multiSort: false,
  mustSort: false,
})


const form = ref({
  registrationNumber: '',
  brand: '',
  model: '',
  type: '',
})

const headers = computed(() => [
  { text: $t('No.'), value: 'lp' },
  { text: $t('Registration'), value: 'registrationNumber' },
  { text: $t('Make'), value: 'brand' },
  { text: $t('Model'), value: 'model' },
  { text: $t('Type'), value: 'type' },
  { text: $t('Created'), value: 'createdAt' },
  { text: $t('Updated'), value: 'updatedAt' },
  { text: $t('Actions'), value: 'actions', sortable: false },
])

const getLpNumber = (index: number) => {
  return (options.value.page - 1) * options.value.itemsPerPage + index + 1
}

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.ctrlKey || e.metaKey) {
    isCtrlDown.value = true
  }
}

const handleKeyUp = (e: KeyboardEvent) => {
  isCtrlDown.value = false
}
// BONUS for multi select
const handleRowClick = (props: any) => {
  if (!isCtrlDown.value) {
    return
  }
  const id = props?.id
  const index = selected.value.indexOf(id)
  if (index === -1) {
    selected.value.push(id)
  } else {
    selected.value.splice(index, 1)
  }

  nextTick(() => {
    bulkMode.value = selected.value.length > 0
  })
}
// BONUS for multi select
const getRowClass = (item: Vehicle) => {
  return selected.value.includes(item.id) ? 'selected-row' : ''
}

// custom sort function to handle dates
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
    .get('/vehicles/list')
    .then((response) => {
      vehicles.value = response.data.results
    })
    .catch((error) => {
      $toast.error('Failed to fetch vehicles list')
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
      //low number of records so re-fetch but ideally should be server-side pagination or careful frontend records handlers, maybe store
      fetchData()
    })
    .catch(() => {
      $toast.error('Error deleting')
    })
}

// BONUS Bulk delete
const deleteSelected = () => {
  const ids = selected.value
  Promise.all(
    ids.map((id) => $api.delete(`/vehicles/delete/${id}`))
  )
    .then(() => {
      $toast.success(`Deleted ${ids.length} vehicle(s)`)
      selected.value = []
      //low number of records so re-fetch but ideally should be server-side pagination or careful frontend records handlers, maybe store
      fetchData()
    })
    .catch(() => {
      $toast.error('Error deleting selected vehicles')
    })
}

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

// Save (create or update) this really should be separated into POST and PUT / PATCH
const saveVehicle = () => {
  const url = isEditing.value && editingId.value
    ? `/vehicles/save/${editingId.value}`
    : '/vehicles/save/'

  $api
    .post(url, form.value)
    .then(() => {
      $toast.success(isEditing.value ? 'Vehicle updated' : 'Vehicle added')
      dialog.value = false
      //low number of records so re-fetch but ideally should be server-side pagination or careful frontend records handlers, maybe store
      fetchData()
    })
    .catch(() => {
      $toast.error('Error saving vehicle')
    })
}
// BONUS for basic multi-row selection
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

