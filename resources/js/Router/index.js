import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
  {
    path: '/users',
    name: 'Usuario',
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

// router.beforeEach((to, from, next) => {
//     //Aqui checas, si tiene una sesion o un token y el usuario quiere ir a la ruta login, no lo permites //y lo mandas a la raiz
//     if (localStorage.token) {
//         next()
//     }
//     window.location.href = '/login'
// })

export default router
