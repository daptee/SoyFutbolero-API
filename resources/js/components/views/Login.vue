<template>
   <v-app>
      <v-content>
         <v-container fluid fill-height>
            <v-layout align-center justify-center>
               <v-flex xs12 sm8 md4>
                  <v-card class="elevation-12">
                     <v-toolbar dark color="primary">
                        <v-toolbar-title>Soy Futbolero</v-toolbar-title>
                     </v-toolbar>
                     <v-card-text>
                        <v-form>
                           <v-text-field v-model="username" prepend-icon="mdi-account" name="login" label="Login" type="text"></v-text-field>
                           <v-text-field v-model="password" id="password" prepend-icon="mdi-lock" name="password" label="Password" type="password"></v-text-field>
                        </v-form>
                     </v-card-text>
                     <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" @click="login()">Login</v-btn>
                     </v-card-actions>
                  </v-card>
               </v-flex>
            </v-layout>
         </v-container>
      </v-content>
   </v-app>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import decodeError from '../../Helpers/errorDecode'

export default {
    data: () => ({
        username: '',
        password: '',

    }),
    created () {

    },
    computed: {

    },
    methods: {
        async login(){
            try{
                // if (this.username == '' || this.password == '') {
                //     return
                // }

                let auth = {
                    usuario: this.username,
                    password: this.password
                }

                var response = await axios.post("/api/login",auth)
                let token = response.data.token_type + ' ' + response.data.access_token

                localStorage.token = token
                window.location.href  = '/'

            }catch(error){

                return decodeError.decodeError(error.response.data)
            }
        }
    },
    watch: {

    },
  }
</script>
