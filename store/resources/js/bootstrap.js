import axios from 'axios';
import { APP_BASE_PATH, buildStoreUrl } from './services/runtimeConfig';
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

const clearAuthStorage = () => {
	const keys = ['auth_token', 'armely_user', 'auth_session_expiry', 'auth_restricted', 'auth_remember', 'auth_force_pw'];
	keys.forEach((k) => {
		localStorage.removeItem(k);
		sessionStorage.removeItem(k);
	});
};

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
	window.location.replace(`${buildStoreUrl('login')}?reason=${encodeURIComponent(reason)}`);
};

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

		if ((status === 401 || status === 419) && !isAuthEntryRequest) {
			clearAuthStorage();
			redirectToLogin(status === 419 ? 'session-expired' : 'unauthorized');
		}

		return Promise.reject(error);
	}
);
