<template>
  <span>{{ formatted }}</span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface DateTimeObject {
  date: string
  timezone_type?: number
  timezone?: string
}

const props = defineProps<{
  value: DateTimeObject
}>()

//very basic date formatting, this should take user's locale / preference into account
const formatted = computed(() => {
  if (!props.value || typeof props.value.date !== 'string') return '—'

  try {
    const iso = props.value.date.replace(' ', 'T')
    const date = new Date(iso)

    if (isNaN(date.getTime())) return 'Invalid date'

    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    const hours = String(date.getHours()).padStart(2, '0')
    const minutes = String(date.getMinutes()).padStart(2, '0')

    return `${year}-${month}-${day} ${hours}:${minutes}`
  } catch {
    return 'Invalid date'
  }
})
</script>
