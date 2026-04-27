import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router/index.js';
import App from './App.vue';

// Create Vue app instance
const app = createApp(App);

// Use Pinia for state management
const pinia = createPinia();
app.use(pinia);

// Use Vue Router
app.use(router);

// Mount the app
app.mount('#app');
