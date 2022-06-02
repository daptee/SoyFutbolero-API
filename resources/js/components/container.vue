<template>
  <v-app id="inspire">
    <v-navigation-drawer v-model="drawer" dark app>
      <!-- Slide Menu  -->
            <v-list shaped>
            <v-subheader>Soy Futbolero</v-subheader>
            <v-list-item-group
                v-model="selectedItem"
                color="primary"
            >
                <v-list-item
                v-for="(item, i) in items"
                :key="i"
                >
                <v-list-item-icon>
                    <v-icon v-text="item.icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                </v-list-item-content>
                </v-list-item>
            </v-list-item-group>
            </v-list>


    </v-navigation-drawer>

    <v-app-bar  dark app>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

      <v-toolbar-title>Soy Futbolero</v-toolbar-title>
    </v-app-bar>

    <v-main>
      <!--  -->
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
