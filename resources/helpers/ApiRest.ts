import axios from 'axios'

//should be env
const apiBase = 'http://localhost:8008'

const $api = {
  get: (url: string, config = {}) => axios.get(apiBase + url, config),
  post: (url: string, data = {}, config = {}) => axios.post(apiBase + url, data, config),
  put: (url: string, data = {}, config = {}) => axios.put(apiBase + url, data, config),
  patch: (url: string, data = {}, config = {}) => axios.patch(apiBase + url, data, config),
  delete: (url: string, config = {}) => axios.delete(apiBase + url, config),
}

export default $api
