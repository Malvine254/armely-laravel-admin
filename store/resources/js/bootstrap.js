import axios from 'axios';
import { APP_BASE_PATH } from './services/runtimeConfig';
window.axios = axios;

const appOrigin = typeof window !== 'undefined' ? window.location.origin : '';
const normalizedAppBasePath = APP_BASE_PATH.replace(/\/+$/, '');

// Ensure axios requests like "/api/v1/..." resolve under "/store" in subpath deployments.
window.axios.defaults.baseURL = `${appOrigin}${normalizedAppBasePath}`;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
