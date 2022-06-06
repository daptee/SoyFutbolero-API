<template>
  <v-app id="inspire">
    <v-navigation-drawer v-model="drawer" dark app>
      <!-- Slide Menu  -->
            <v-list>
                <v-subheader>Soy Futbolero</v-subheader>

                    <template>
                        <v-list-item :to="{name: 'Login'}">
                            <v-list-item-icon>
                                <v-icon>mdi-account</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>Usuarios</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>

                    <template>
                        <v-list-item :to="{name: 'Equipos'}">
                            <v-list-item-icon>
                                <v-icon>mdi-shield-account</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>Equipos</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>

                    <template>
                        <v-list-item :to="{name: 'Estadios'}">
                            <v-list-item-icon>
                                <v-icon>mdi-bank</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>Estadios</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
            </v-list>


    </v-navigation-drawer>

    <v-app-bar dark app>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

      <v-toolbar-title>Soy Futbolero</v-toolbar-title>
    </v-app-bar>

    <v-main>
      <!--  -->
      <router-view/>
    </v-main>
  </v-app>
</template>

<script>
  export default {
    data: () => ({
        drawer: null ,
        selectedItem: 1,
        items: [
            { text: 'Usuarios', icon: 'mdi-account' },
            { text: 'Equipos', icon: 'mdi-shield-account' },
            { text: 'Estadios', icon: 'mdi-bank' }
            ,
        ],
    }),
  }
</script>
