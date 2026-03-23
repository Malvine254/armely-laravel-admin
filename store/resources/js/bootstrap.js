import axios from 'axios';
import { APP_BASE_PATH } from './services/runtimeConfig';
window.axios = axios;

const appOrigin = typeof window !== 'undefined' ? window.location.origin : '';
const normalizedAppBasePath = APP_BASE_PATH.replace(/\/+$/, '');

// Ensure axios requests like "/api/v1/..." resolve under "/store/public" in
// subpath deployments where Apache serves Laravel from the public directory.
window.axios.defaults.baseURL =
	normalizedAppBasePath === '/store'
		? `${appOrigin}/store/public`
		: appOrigin;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
