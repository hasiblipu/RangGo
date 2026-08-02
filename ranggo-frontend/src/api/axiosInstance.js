import axios from 'axios';

const api = axios.create({
  baseURL: 'https://ranggo.clickio.com.bd/api', // আপানার ব্যাকএন্ড URL দিন
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request Interceptor for Auth Token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('ranggo_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

export default api;