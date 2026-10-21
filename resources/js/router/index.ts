import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { toast } from 'vue-sonner';

import HomeView from '@/views/HomeView.vue';
import ProductsView from '@/views/ProductsView.vue';
import ProductDetailView from '@/views/ProductDetailView.vue';
import LoginView from '@/views/LoginView.vue';
import RegisterView from '@/views/RegisterView.vue';
import AdminProductsView from '@/views/AdminProductsView.vue';
import ProductFormView from '@/views/ProductFormView.vue';
import NotFoundView from '@/views/NotFoundView.vue';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/products',
    name: 'products',
    component: ProductsView,
  },
  {
    path: '/products/:id',
    name: 'product-detail',
    component: ProductDetailView,
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guestOnly: true },
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView,
    meta: { guestOnly: true },
  },
  {
    path: '/admin/products',
    name: 'admin-products',
    component: AdminProductsView,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/products/new',
    name: 'admin-product-create',
    component: ProductFormView,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/products/:id/edit',
    name: 'admin-product-edit',
    component: ProductFormView,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFoundView,
  },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(_to, _from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }
    return { top: 0, behavior: 'smooth' };
  },
});

router.beforeEach((to, _from, next) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.token) {
    toast.error('Authentication required. Please sign in.');
    return next({ name: 'login', query: { redirect: to.fullPath } });
  }

  if (to.meta.requiresAdmin && auth.user?.role !== 'admin') {
    // If token exists but user role is not yet populated or is user
    if (auth.token && !auth.user) {
      return next();
    }
    toast.error('Forbidden. Admin authorization required.');
    return next({ name: 'home' });
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return next({ name: 'home' });
  }

  next();
});

export default router;
