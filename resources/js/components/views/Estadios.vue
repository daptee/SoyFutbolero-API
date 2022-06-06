<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-data-table :headers="headers"  :items="stadium" sort-by="nombre" class="elevation-1" width="100%">
                    <template v-slot:item.equipo="{ item }">
                        {{ item.team == null ?  '' : item.team.nombre  }}
                    </template>
                    <template v-slot:item.estado="{ item }">
                      <v-chip :color="getColor(item.estado)" dark>
                        {{ item.estado == 1 ?  'Habilitado' : 'Deshabilitado'  }}
                      </v-chip>
                    </template>
                    <template v-slot:top>
                    <v-toolbar flat>
                        <v-toolbar-title>Estadios</v-toolbar-title>
                        <v-divider class="mx-4" inset vertical></v-divider>
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" max-width="500px">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                                Nuevo
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                            </v-card-title>
                            <v-card-text>
                            <v-container>
                                <v-row>
                                  <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.nombre" label="Nombre"></v-text-field>
                                  </v-col>
                                  <v-col cols="12" sm="6" md="4">
                                        <v-autocomplete v-model="id_equipo" :items="teams" item-text="nombre" clearable item-value="id" label="Equipo" persistent-hint single-line/>
                                  </v-col>
                                </v-row>
                            </v-container>
                            </v-card-text>

                            <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="close">
                                Cancelar
                            </v-btn>
                            <v-btn color="blue darken-1" text @click="save">
                                Guardar
                            </v-btn>
                            </v-card-actions>
                        </v-card>
                        </v-dialog>
                        <v-dialog v-model="dialogDelete" max-width="60%">
                        <v-card>
                            <v-card-title class="headline">Esta por deshabilitar el Estadio: "{{ editedItem.nombre}}", esta seguro?</v-card-title>
                            <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                            <v-btn color="red darken-1" text @click="deleteItemConfirm">Aceptar</v-btn>
                            <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                        </v-dialog>
                    </v-toolbar>
                    </template>
                    <template v-slot:item.actions="{ item }">
                    <v-icon color="blue darken-2" class="mr-2" @click="editItem(item)">
                        mdi-pencil
                    </v-icon>
                    <v-icon color="red darken-2" class="mr-2" @click="deshabilitarItem(item)" v-if="item.estado == 1">
                        mdi-cancel
                    </v-icon>
                    <v-icon color="green darken-2" class="mr-2" @click="habilitar(item)" v-if="item.estado == 0">
                        mdi-check
                    </v-icon>
                    </template>
                    <template v-slot:no-data>
                    <v-btn color="primary" @click="getCategories">
                        Recargar
                    </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>

</template>


<script>
import { mapState, mapActions } from 'vuex'

export default {
    data: () => ({
      dialog: false,
      dialogDelete: false,
      headers: [
        { text: 'Nombre', sortable: false, value: 'nombre' },
        { text: 'Equipo', value: 'equipo' },
        { text: 'Estado', value: 'estado' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      id_equipo:null,
      editedIndex: -1,
      editedItem:{
        estado: '',
        foto: '',
        id:"",
        nombre: '',
        team: {}
      },
      defaultItem: {
        estado: '',
        foto: '',
        id:"",
        nombre: '',
        team: {}
      },
    }),
    computed: {
      ...mapState(['teams','stadium']),
      formTitle () {
        return this.editedIndex === -1 ? 'Agregar Estadios' : 'Editar Estadios'
      },
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
    },
    created () {
        this.getStadium()
        if(this.teams.length == 0){
            this.getTeams()
        }
    },
    methods: {
        ...mapActions(['getStadium','getTeams','createStadium','editStadium']),
        getColor (state) {
            return state == 1 ?  'green' : 'red'
        },
        editItem (item) {
            this.editedIndex = this.stadium.indexOf(item)
            this.id_equipo = isNaN(item.team) || item.team == null ? null : item.team.id
            this.editedItem = Object.assign({}, item)
            delete this.editedItem.team
            this.dialog = true
        },
        deshabilitarItem(item) {
            this.editedItem = {...item}
            delete this.editedItem.team
            this.dialogDelete = true
        },
        async habilitar(item){
            await this.editStadium({estado: 1, id: item.id })
            this.closeDelete()
        },
        async deleteItemConfirm() {
            await this.editStadium({estado: 0, id: this.editedItem.id })
            this.closeDelete()
        },
        close () {
            this.dialog = false
            this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
            })
        },
        closeDelete () {
            this.dialogDelete = false
            this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
            })
        },
        async save () {
            if (this.editedIndex > -1) {
                this.editedItem.foto =  this.editedItem.foto  == '' ? this.editedItem.nombre : this.editedItem.foto
                this.editedItem.id_equipo = this.id_equipo
                await this.editStadium(this.editedItem)
            } else {
                this.editedItem.id_equipo = isNaN(this.id_equipo) || this.id_equipo == null ? 0 : this.id_equipo
                this.editedItem.foto = this.editedItem.nombre
                this.editedItem.estado = 1
                delete this.editedItem.id
                delete this.editedItem.team
                await this.createStadium(this.editedItem)
            }
            this.close()
        }
    },
  }
</script>
