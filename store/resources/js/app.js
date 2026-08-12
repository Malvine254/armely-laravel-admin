import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router/index.js';
import App from './App.vue';

if (typeof window !== 'undefined') {
	window.__ARMELY_STORE_BUILD__ = '2026-08-12.1';
}

// Create Vue app instance
const app = createApp(App);

// Use Pinia for state management
const pinia = createPinia();
app.use(pinia);

// Use Vue Router
app.use(router);

// Mount the app
app.mount('#app');
