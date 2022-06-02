<template>
    <div class="register-page">
        <div class="register-box">
            <div class="card">
                <div class="card-body register-card-body">
                    <p class="login-box-msg">Soy Futbolero</p>

                    <form method="post" ref="form">
                        <div class="input-group">
                            <input type="email" class="form-control" v-model="usuario" placeholder="Usuario" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <p class="small text-danger mb-3" v-html="errors.usuario"></p>

                        <div class="input-group">
                            <input type="password" class="form-control" v-model="password" placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <p class="small text-danger mb-3" v-html="errors.password"></p>

                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary btn-block" @click="validate()">Login</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>
                </div>
                <!-- /.form-box -->
            </div><!-- /.card -->
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'
import axios from 'axios'

export default {
    name: "Login",
    data() {
        return {
            usuario: '',
            password: '',
            success: false,
            failure: false,
            message: '',
            token: document.head.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: false,
            errors: {
                usuario:'',
                password:''
            }
        }
    },
    methods: {
        ...mapActions(['saveToken']),
        async validate(){
            try{
                let user = {
                    usuario: this.usuario,
                    password: this.password,
                    _token: this.token
                }
                var response = await axios.post('/api/login', user)
                let credentials = {
                    token: '123142345345'
                }
                this.saveToken(credentials)

                console.log(credentials)
            }catch(error){
                console.log('Este es el error: '+ error.response.data.message)
            }
        }
    }
}
</script>
