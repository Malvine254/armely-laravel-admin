import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/authStore.js';
import { useToastStore } from '../stores/toastStore.js';
import { APP_BASE_PATH } from '../services/runtimeConfig.js';

// Import views
import Login from '../views/auth/Login.vue';
import Register from '../views/auth/Register.vue';
import ActivateAccount from '../views/auth/ActivateAccount.vue';
import ForgotPassword from '../views/auth/ForgotPassword.vue';
import ResetPassword from '../views/auth/ResetPassword.vue';
import AdminLogin from '../views/auth/AdminLogin.vue';
import AdminDashboard from '../pages/AdminDashboard.vue';
import AdminQuotesPage from '../pages/AdminQuotesPage.vue';
import AdminOrdersPage from '../pages/AdminOrdersPage.vue';
import AdminCustomersPage from '../pages/AdminCustomersPage.vue';
import AdminReportsPage from '../pages/AdminReportsPage.vue';
import AdminSettingsPage from '../pages/AdminSettingsPage.vue';
import AdminInvoicesPage from '../pages/AdminInvoicesPage.vue';
import Products from '../views/catalog/Products.vue';
import ProductDetail from '../views/catalog/ProductDetail.vue';
import Cart from '../views/quotes/Cart.vue';
import Quotes from '../views/quotes/Quotes.vue';
import Invoices from '../views/invoices/Invoices.vue';
import Payment from '../views/payments/Payment.vue';
import Favorites from '../views/auth/Favorites.vue';
import Account from '../views/auth/Account.vue';
import Messages from '../views/messages/Messages.vue';
import Privacy from '../views/Privacy.vue';
import CookieSettings from '../views/CookieSettings.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: Products,
    meta: { requiresAuth: false },
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresAuth: false },
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { requiresAuth: false },
  },
  {
    path: '/activate-account',
    name: 'activate-account',
    component: ActivateAccount,
    meta: { requiresAuth: false },
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPassword,
    meta: { requiresAuth: false },
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: ResetPassword,
    meta: { requiresAuth: false },
  },
  {
    path: '/admin/login',
    name: 'admin-login',
    component: AdminLogin,
    alias: ['/admin-login'],
    meta: { requiresAuth: false },
  },
  {
    path: '/admin',
    name: 'admin-dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard-page',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/quotes/pending',
    name: 'admin-quotes',
    component: AdminQuotesPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/orders',
    name: 'admin-orders',
    component: AdminOrdersPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/customers',
    name: 'admin-customers',
    component: AdminCustomersPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/reports',
    name: 'admin-reports',
    component: AdminReportsPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/settings',
    name: 'admin-settings',
    component: AdminSettingsPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/invoices',
    name: 'admin-invoices',
    component: AdminInvoicesPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/products',
    name: 'products',
    component: Products,
    meta: { requiresAuth: false },
  },
  {
    path: '/products/:id',
    name: 'product-detail',
    component: ProductDetail,
    meta: { requiresAuth: false },
  },
  {
    path: '/cart',
    name: 'cart',
    component: Cart,
    meta: { requiresAuth: false },
  },
  {
    path: '/quotes',
    name: 'quotes',
    component: Quotes,
    meta: { requiresAuth: true },
  },
  {
    path: '/invoices',
    name: 'invoices',
    component: Invoices,
    meta: { requiresAuth: true },
  },
  {
    path: '/payment',
    name: 'payment',
    component: Payment,
    meta: { requiresAuth: true },
  },
  {
    path: '/favorites',
    name: 'favorites',
    component: Favorites,
    meta: { requiresAuth: true },
  },
  {
    path: '/account',
    name: 'account',
    component: Account,
    meta: { requiresAuth: true },
  },
  {
    path: '/orders',
    name: 'orders',
    redirect: { name: 'quotes' },
    meta: { requiresAuth: true },
  },
  {
    path: '/messages',
    name: 'messages',
    component: Messages,
    meta: { requiresAuth: true },
  },
  {
    path: '/privacy',
    name: 'privacy',
    component: Privacy,
    meta: { requiresAuth: false },
  },
  {
    path: '/cookie-settings',
    name: 'cookie-settings',
    component: CookieSettings,
    meta: { requiresAuth: false },
  },
];

const router = createRouter({
  history: createWebHistory(APP_BASE_PATH),
  routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  // Redirect to login if accessing protected route while unauthenticated
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    localStorage.setItem('redirectAfterLogin', to.fullPath);
    if (to.meta.requiresAdmin) {
      next({ name: 'admin-login', query: { redirect: to.fullPath } });
      return;
    }
    next({ name: 'login', query: { redirect: to.fullPath } });
    return;
  }
  
  // Redirect if already logged in and trying to access login/register
  if ((to.name === 'login' || to.name === 'register') && authStore.isAuthenticated) {
    next({ name: authStore.isAdmin ? 'admin-dashboard' : 'products' });
    return;
  }

  if (to.name === 'admin-login' && authStore.isAuthenticated) {
    next({ name: authStore.isAdmin ? 'admin-dashboard' : 'products' });
    return;
  }
  
  // Check admin requirement
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next({ name: 'products' });
    return;
  }
  
  // Check feature access
  if (to.meta.requiredFeature && !authStore.hasFeatureAccess(to.meta.requiredFeature)) {
    next({ name: 'products' });
    return;
  }

  // Activation pending users should stay on public/account pages until email is verified.
  if (authStore.isActivationPending) {
    const allowedNames = ['home', 'products', 'product-detail', 'account', 'activate-account', 'login', 'privacy', 'cookie-settings'];
    if (to.name && !allowedNames.includes(to.name)) {
      const toastStore = useToastStore();
      toastStore.addToast('Please activate your account from the email link before accessing this page.', 'warning');
      next({ name: 'account' });
      return;
    }
  }

  // Restricted users can sign in but are limited to read-only friendly pages.
  if (authStore.isRestricted) {
    const allowedNames = ['home', 'products', 'product-detail', 'account', 'login', 'privacy', 'cookie-settings'];
    if (to.name && !allowedNames.includes(to.name)) {
      const toastStore = useToastStore();
      toastStore.addToast('Your account is restricted. Please contact your administrator.', 'warning');
      next({ name: 'account' });
      return;
    }
  }
  
  // Check if session has expired
  if (authStore.isAuthenticated && authStore.sessionExpiry) {
    const secondsRemaining = authStore.getSessionTimeRemaining();
    if (secondsRemaining && secondsRemaining < 0) {
      authStore.logout();
      next({ name: 'login', query: { 'session-expired': 'true' } });
      return;
    }
  }
  
  next();
});

// Session expiration warning
router.afterEach((to, from) => {
  const authStore = useAuthStore();
  
  if (authStore.isAuthenticated && authStore.sessionExpiry) {
    const secondsRemaining = authStore.getSessionTimeRemaining();
    // Warn if less than 5 minutes remaining
    if (secondsRemaining && secondsRemaining < 300 && secondsRemaining > 0) {
      const toastStore = useToastStore();
      toastStore.addToast(
        `Your session will expire in ${Math.floor(secondsRemaining / 60)} minutes. Please save your work.`,
        'warning'
      );
    }
  }
});

export default router;
