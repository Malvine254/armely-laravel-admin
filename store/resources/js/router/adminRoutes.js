// Admin Routes Configuration
// Add this to your src/router/index.js or create a new router file

import AdminDashboard from '@/pages/AdminDashboard.vue'
import AdminQuotesPage from '@/pages/AdminQuotesPage.vue'
import AdminOrdersPage from '@/pages/AdminOrdersPage.vue'
import AdminCustomersPage from '@/pages/AdminCustomersPage.vue'
import AdminUsersPage from '@/pages/AdminUsersPage.vue'
import AdminReportsPage from '@/pages/AdminReportsPage.vue'
import AdminInvoicesPage from '@/pages/AdminInvoicesPage.vue'
import AdminAnnouncementsPage from '@/pages/AdminAnnouncementsPage.vue'

// Admin Routes - Protected by authentication middleware
export const adminRoutes = [
  {
    path: '/admin',
    component: { template: '<router-view />' },
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: AdminDashboard,
        meta: {
          title: 'Admin Dashboard',
          breadcrumb: ['Admin', 'Dashboard']
        }
      },
      {
        path: 'quotes/pending',
        name: 'AdminQuotes',
        component: AdminQuotesPage,
        meta: {
          title: 'Pending Quotes',
          breadcrumb: ['Admin', 'Quotes', 'Pending']
        }
      },
      {
        path: 'quotes/:id',
        name: 'AdminQuoteDetail',
        component: AdminQuotesPage,
        meta: {
          title: 'Quote Details',
          breadcrumb: ['Admin', 'Quotes', 'Detail']
        }
      },
      {
        path: 'orders',
        name: 'AdminOrders',
        component: AdminOrdersPage,
        meta: {
          title: 'All Orders',
          breadcrumb: ['Admin', 'Orders']
        }
      },
      {
        path: 'customers',
        name: 'AdminCustomers',
        component: AdminCustomersPage,
        meta: {
          title: 'Customers',
          breadcrumb: ['Admin', 'Customers']
        }
      },
      {
        path: 'users',
        name: 'AdminUsers',
        component: AdminUsersPage,
        meta: {
          title: 'Admin Users',
          breadcrumb: ['Admin', 'Users']
        }
      },
      {
        path: 'reports',
        name: 'AdminReports',
        component: AdminReportsPage,
        meta: {
          title: 'Revenue Reports',
          breadcrumb: ['Admin', 'Reports']
        }
      },
      {
        path: 'invoices',
        name: 'AdminInvoices',
        component: AdminInvoicesPage,
        meta: {
          title: 'Invoice Management',
          breadcrumb: ['Admin', 'Invoices']
        }
      },
      {
        path: 'tables',
        name: 'AdminTables',
        component: AdminAnnouncementsPage,
        alias: ['announcements'],
        meta: {
          title: 'Tables',
          breadcrumb: ['Admin', 'Tables']
        }
      }
    ]
  }
]

/**
 * Router Guard - Check for Admin Access
 * Add this to your router setup:
 * 
 * router.beforeEach((to, from, next) => {
 *   const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)
 *   const user = store.state.auth.user // or get from localStorage
 *   
 *   if (requiresAdmin && (!user || user.role !== 'admin')) {
 *     next('/login') // or '/dashboard'
 *   } else {
 *     next()
 *   }
 * })
 */
