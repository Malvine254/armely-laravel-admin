import axios from 'axios';
import { APP_BASE_PATH, buildStoreUrl } from './services/runtimeConfig';
import { AUTH_CONTEXTS, clearScopedAuthStorage, getActiveAuthContext, getAuthStorageKeys } from './services/authContext';
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

const redirectToLogin = (reason = 'session-expired') => {
	if (typeof window === 'undefined') {
		return;
	}

	if (window.__ARMELY_AUTH_REDIRECTING__) {
		return;
	}

	const currentPath = String(window.location.pathname || '').toLowerCase();
	if (currentPath.endsWith('/login') || currentPath.endsWith('/admin/login')) {
		return;
	}

	window.__ARMELY_AUTH_REDIRECTING__ = true;
	const loginPath = getActiveAuthContext() === AUTH_CONTEXTS.ADMIN ? 'admin/login' : 'login';
	window.location.replace(`${buildStoreUrl(loginPath)}?reason=${encodeURIComponent(reason)}`);
};

window.axios.interceptors.request.use((config) => {
	const context = getActiveAuthContext();
	const tokenKey = getAuthStorageKeys(context).token;
	const token = localStorage.getItem(tokenKey) || sessionStorage.getItem(tokenKey);
	if (token) {
		config.headers = config.headers || {};
		config.headers.Authorization = `Bearer ${token}`;
	}
	return config;
});

window.axios.interceptors.response.use(
	(response) => response,
	(error) => {
		const status = error?.response?.status;
		const requestUrl = String(error?.config?.url || '').toLowerCase();

		const isAuthEntryRequest = requestUrl.includes('/auth/login')
			|| requestUrl.includes('/auth/register')
			|| requestUrl.includes('/auth/forgot-password')
			|| requestUrl.includes('/auth/reset-password')
			|| requestUrl.includes('/auth/activate')
			|| requestUrl.includes('/auth/resend-activation')
			|| requestUrl.includes('/auth/logout');
		const context = getActiveAuthContext();

		if (status === 401 && !isAuthEntryRequest) {
			clearScopedAuthStorage(context);
			redirectToLogin('unauthorized');
		}

		return Promise.reject(error);
	}
);
