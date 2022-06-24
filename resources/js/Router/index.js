import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
//   {
//     path: '/users',
//     name: 'Usuario',
//     meta: {
//       allowAllUsers: true
//     },
//     component: () => import('../components/views/Login.vue')
//   },
  {
    path: '/login',
    name: 'Login',
    meta: {
      allowAllUsers: true
    },
    component: () => import('../components/views/Login.vue')
  },
  {
    path: '/stadiums',
    name: 'Estadios',
    meta: {
      allowAllUsers: true
    },
    component: () => import('../components/views/Estadios.vue')
  },
  {
    path: '/teams',
    name: 'Equipos',
    meta: {
      allowAllUsers: true
    },
    component: () => import('../components/views/Equipos.vue')
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

function existToken() {
    return !!localStorage.token;
}

router.beforeEach((to, from, next) => {
    if (to.path != '/login' && existToken()) {
        next();
    } else if (to.path == '/login' && existToken()){
        next('/');
    } else {
        next('login');
    }
});

export default router
