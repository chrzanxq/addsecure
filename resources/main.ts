import Vue from 'vue'
import Notifications from 'vue-notification'
import App from './App.vue'
import vuetify from './plugins/vuetify'

Vue.use(Notifications)

new Vue({
  vuetify,
  render: h => h(App)
}).$mount('#app')
