<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-data-table :headers="headers"  :items="equipos" sort-by="nombre" class="elevation-1" width="100%">
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
                                    <v-select v-model="articulo_categoria" :items="categories" item-text="Estado" item-value="_id" label="Categorias" persistent-hint return-object single-line/>
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
                            <v-card-title class="headline">Esta por deshabilitar el articulo: "{{ editedItem.nombre}}", esta seguro?</v-card-title>
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

export default {
    data: () => ({
      dialog: false,
      dialogDelete: false,
      headers: [
        { text: 'Nombre', sortable: false, value: 'nombre' },
        { text: 'Estado', value: 'estado' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      articulo_categoria:'',
      editedIndex: -1,
      editedItem: {
        name: '',
        codigo: '',
        categoria:{
          nombre:""
        },
        descripcion: '',
        stock: 0,
        precio_venta:0
      },
      defaultItem: {
        name: '',
        codigo: '',
        categoria:{
          nombre:""
        },
        descripcion: '',
        stock: 0,
        precio_venta:0
      },
    }),
    computed: {
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
      this.getArticulos()
      this.getCategories()
    },
    methods: {
      getColor (state) {
        return state == 1 ?  'green' : 'red'
      },
      editItem (item) {
        this.editedIndex = this.articulos.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },
      deshabilitarItem(item) {
        this.editedItem = {...item}
        this.dialogDelete = true
      },
      async habilitar(item){
        try{
          await activateArticulo(item)
          this.getArticulos()
        }catch(error){
          console.log(error)
        }
      },
      async deleteItemConfirm() {
        try{
          await deactivateArticulo(this.editedItem)
          this.getArticulos()
          this.closeDelete()
        }catch(error){
          console.log(error)
        }
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
          await this.editArticulo(this.editedItem)
        } else {
          await this.createArticulo(this.editedItem)
        }
        this.close()
      },
      async createArticulo(item){
        try{
          item = {...item, categoria: this.articulo_categoria._id }
          let data = await createArticulo(item)
          this.pushArticulo(data.data)
        }catch(error){
          console.log(error)
        }
      },
      async editArticulo(item){
        try{
          await editArticulo(item)
          this.getArticulos()
        }catch(error){
          console.log(error)
        }
      }
    },
  }
</script>
