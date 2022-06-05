import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        teams: [],
        stadium: [],
        type_teams: [
            {id: 1, nombre: "EQUIPO" },
            {id: 2, nombre: "PAÍS" }
        ],
    },
    mutations: {
        setTeams(state, payload) {
            state.teams.length = 0
            state.teams = payload
        },
        addTeam(state,payload){
            state.teams.push(payload)
        },
        updateTeam(state,payload){
            index = state.teams.find(team => team.id == payload.id)
            state.teams[index] = {...payload}
        }
    },
    actions: {
        async getTeams({ commit }){
            let data = await axios.get("/api/teams")
            commit('setTeams', data.data.data)
          },
        async createTeam({ commit },payload){
            try{
                let data = await axios.post("/api/teams",payload)

                commit('addTeam', data.data.data)
            }catch(error){

            }
        },
        async editTeam({ commit },payload){
            try{
                let data = await axios.put("/api/teams/"+payload.id, payload)
                this.dispatch('getTeams')
            }catch(error){

            }
        }
    },
    modules: {

    }
})
