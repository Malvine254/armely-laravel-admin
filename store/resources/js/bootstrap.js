import axios from 'axios';
import { APP_BASE_PATH } from './services/runtimeConfig';
window.axios = axios;

const appOrigin = typeof window !== 'undefined' ? window.location.origin : '';
const isLocalStoreServer =
	typeof window !== 'undefined' &&
	(window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost') &&
	window.location.port === '8001';

// For production deployments where the store app is mounted at /store,
// use /store as the axios base URL so /api/v1/* resolves to /store/api/v1/*.
const basePathNormalized = APP_BASE_PATH.replace(/\/+$/, ''); // Remove trailing slashes
const isStoreSubpath = basePathNormalized === '/store';

window.axios.defaults.baseURL =
	isLocalStoreServer
		? appOrigin
		: isStoreSubpath
		? `${appOrigin}/store`
		: appOrigin;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
