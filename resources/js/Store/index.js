import Vue from 'vue';
import Vuex from 'vuex'

Vue.use(Vuex);

const store = new Vuex.Store({
  state: {
    token:''
  },
  mutations: {
    setToken (state, payload) {
        if (payload === '') {
          state.token = ''
        } else {
          state.token = payload.token
        }
      }
  },
  actions: {
    saveToken ({ commit }, payload) {
        console.log('tokkkken'+ payload.token)
        //localStorage.setItem('access_token', payload.token)
        //commit('setToken', payload.token)
    }
  }
})

export default store;
