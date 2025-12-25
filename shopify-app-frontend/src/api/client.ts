
import axios from 'axios';

const api = axios.create({
  baseURL: '/api',   // served by Laravel backend
  withCredentials: true,
});
export default api;
