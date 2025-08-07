// i have installed the 'vue-notify' library but this should probably be a custom global using v-snackbar or v-alert
import { getCurrentInstance } from 'vue'

export function useToast() {
  const instance = getCurrentInstance()
  if (!instance) {
    throw new Error('useToast must be used inside setup()')
  } 
  const notify = instance.proxy.$notify

  return {
    success: (text: string, duration = 4000) =>
      notify({
        group: 'app',
        type: 'success',
        text,
        duration,
        data: {
          icon: 'mdi-check-circle',
        },
      }),

    error: (text: string, duration = 5000) =>
      notify({
        group: 'app',
        type: 'error',
        text,
        duration,
        data: {
          icon: 'mdi-alert-circle',
        },
      }),

    info: (text: string, duration = 4000) =>
      notify({
        group: 'app',
        type: 'info',
        text,
        duration,
        data: {
          icon: 'mdi-information',
        },
      }),
  }
}
