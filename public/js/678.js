/*! For license information please see 678.js.LICENSE.txt */
"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[678],{678:(t,e,r)=>{r.r(e),r.d(e,{default:()=>f});var n=r(629);function o(t){return o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},o(t)}function i(){i=function(){return t};var t={},e=Object.prototype,r=e.hasOwnProperty,n="function"==typeof Symbol?Symbol:{},a=n.iterator||"@@iterator",c=n.asyncIterator||"@@asyncIterator",s=n.toStringTag||"@@toStringTag";function l(t,e,r){return Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{l({},"")}catch(t){l=function(t,e,r){return t[e]=r}}function u(t,e,r,n){var o=e&&e.prototype instanceof h?e:h,i=Object.create(o.prototype),a=new O(n||[]);return i._invoke=function(t,e,r){var n="suspendedStart";return function(o,i){if("executing"===n)throw new Error("Generator is already running");if("completed"===n){if("throw"===o)throw i;return L()}for(r.method=o,r.arg=i;;){var a=r.delegate;if(a){var c=x(a,r);if(c){if(c===f)continue;return c}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if("suspendedStart"===n)throw n="completed",r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n="executing";var s=d(t,e,r);if("normal"===s.type){if(n=r.done?"completed":"suspendedYield",s.arg===f)continue;return{value:s.arg,done:r.done}}"throw"===s.type&&(n="completed",r.method="throw",r.arg=s.arg)}}}(t,r,a),i}function d(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}t.wrap=u;var f={};function h(){}function v(){}function p(){}var m={};l(m,a,(function(){return this}));var y=Object.getPrototypeOf,b=y&&y(y(E([])));b&&b!==e&&r.call(b,a)&&(m=b);var g=p.prototype=h.prototype=Object.create(m);function _(t){["next","throw","return"].forEach((function(e){l(t,e,(function(t){return this._invoke(e,t)}))}))}function w(t,e){function n(i,a,c,s){var l=d(t[i],t,a);if("throw"!==l.type){var u=l.arg,f=u.value;return f&&"object"==o(f)&&r.call(f,"__await")?e.resolve(f.__await).then((function(t){n("next",t,c,s)}),(function(t){n("throw",t,c,s)})):e.resolve(f).then((function(t){u.value=t,c(u)}),(function(t){return n("throw",t,c,s)}))}s(l.arg)}var i;this._invoke=function(t,r){function o(){return new e((function(e,o){n(t,r,e,o)}))}return i=i?i.then(o,o):o()}}function x(t,e){var r=t.iterator[e.method];if(void 0===r){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=void 0,x(t,e),"throw"===e.method))return f;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return f}var n=d(r,t.iterator,e.arg);if("throw"===n.type)return e.method="throw",e.arg=n.arg,e.delegate=null,f;var o=n.arg;return o?o.done?(e[t.resultName]=o.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=void 0),e.delegate=null,f):o:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,f)}function k(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function I(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function O(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(k,this),this.reset(!0)}function E(t){if(t){var e=t[a];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var n=-1,o=function e(){for(;++n<t.length;)if(r.call(t,n))return e.value=t[n],e.done=!1,e;return e.value=void 0,e.done=!0,e};return o.next=o}}return{next:L}}function L(){return{value:void 0,done:!0}}return v.prototype=p,l(g,"constructor",p),l(p,"constructor",v),v.displayName=l(p,s,"GeneratorFunction"),t.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===v||"GeneratorFunction"===(e.displayName||e.name))},t.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,p):(t.__proto__=p,l(t,s,"GeneratorFunction")),t.prototype=Object.create(g),t},t.awrap=function(t){return{__await:t}},_(w.prototype),l(w.prototype,c,(function(){return this})),t.AsyncIterator=w,t.async=function(e,r,n,o,i){void 0===i&&(i=Promise);var a=new w(u(e,r,n,o),i);return t.isGeneratorFunction(r)?a:a.next().then((function(t){return t.done?t.value:a.next()}))},_(g),l(g,s,"Generator"),l(g,a,(function(){return this})),l(g,"toString",(function(){return"[object Generator]"})),t.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var n=e.pop();if(n in t)return r.value=n,r.done=!1,r}return r.done=!0,r}},t.values=E,O.prototype={constructor:O,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(I),!t)for(var e in this)"t"===e.charAt(0)&&r.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=void 0)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function n(r,n){return a.type="throw",a.arg=t,e.next=r,n&&(e.method="next",e.arg=void 0),!!n}for(var o=this.tryEntries.length-1;o>=0;--o){var i=this.tryEntries[o],a=i.completion;if("root"===i.tryLoc)return n("end");if(i.tryLoc<=this.prev){var c=r.call(i,"catchLoc"),s=r.call(i,"finallyLoc");if(c&&s){if(this.prev<i.catchLoc)return n(i.catchLoc,!0);if(this.prev<i.finallyLoc)return n(i.finallyLoc)}else if(c){if(this.prev<i.catchLoc)return n(i.catchLoc,!0)}else{if(!s)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return n(i.finallyLoc)}}}},abrupt:function(t,e){for(var n=this.tryEntries.length-1;n>=0;--n){var o=this.tryEntries[n];if(o.tryLoc<=this.prev&&r.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===t||"continue"===t)&&i.tryLoc<=e&&e<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=t,a.arg=e,i?(this.method="next",this.next=i.finallyLoc,f):this.complete(a)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),f},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),I(r),f}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var n=r.completion;if("throw"===n.type){var o=n.arg;I(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,r){return this.delegate={iterator:E(t),resultName:e,nextLoc:r},"next"===this.method&&(this.arg=void 0),f}},t}function a(t,e,r,n,o,i,a){try{var c=t[i](a),s=c.value}catch(t){return void r(t)}c.done?e(s):Promise.resolve(s).then(n,o)}function c(t){return function(){var e=this,r=arguments;return new Promise((function(n,o){var i=t.apply(e,r);function c(t){a(i,n,o,c,s,"next",t)}function s(t){a(i,n,o,c,s,"throw",t)}c(void 0)}))}}function s(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function l(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?s(Object(r),!0).forEach((function(e){u(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):s(Object(r)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}function u(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}const d={data:function(){return{dialog:!1,dialogDelete:!1,headers:[{text:"Nombre",sortable:!1,value:"nombre"},{text:"Tipo",value:"tipo"},{text:"Estado",value:"estado"},{text:"Actions",value:"actions",sortable:!1}],tipo_equipo:"",editedIndex:-1,editedItem:{nombre:"",bandera:"",escudo:"",id_tipo:""},defaultItem:{nombre:"",bandera:"",escudo:"",id_tipo:""}}},computed:l(l({},(0,n.rn)(["teams","type_teams"])),{},{formTitle:function(){return-1===this.editedIndex?"Agregar Equipos":"Editar Equipos"}}),watch:{dialog:function(t){t||this.close()},dialogDelete:function(t){t||this.closeDelete()}},created:function(){this.getTeams()},methods:l(l({},(0,n.nv)(["getTeams","createTeam","editTeam"])),{},{getColor:function(t){return 1==t?"green":"red"},editItem:function(t){this.editedIndex=this.teams.indexOf(t),this.tipo_equipo=t.tipo.id,this.editedItem=Object.assign({},t),delete this.editedItem.tipo,this.dialog=!0},deshabilitarItem:function(t){this.editedItem=l({},t),this.dialogDelete=!0},habilitar:function(t){var e=this;return c(i().mark((function r(){return i().wrap((function(r){for(;;)switch(r.prev=r.next){case 0:return e.editedItem=l({},t),e.editedItem.estado=1,delete e.editedItem.tipo,r.next=5,e.editTeam(e.editedItem);case 5:e.closeDelete();case 6:case"end":return r.stop()}}),r)})))()},deleteItemConfirm:function(){var t=this;return c(i().mark((function e(){return i().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.editedItem.estado=0,delete t.editedItem.tipo,e.next=4,t.editTeam(t.editedItem);case 4:t.closeDelete();case 5:case"end":return e.stop()}}),e)})))()},close:function(){var t=this;this.dialog=!1,this.$nextTick((function(){t.editedItem=Object.assign({},t.defaultItem),t.editedIndex=-1}))},closeDelete:function(){var t=this;this.dialogDelete=!1,this.$nextTick((function(){t.editedItem=Object.assign({},t.defaultItem),t.editedIndex=-1}))},save:function(){var t=this;return c(i().mark((function e(){return i().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t.editedIndex>-1)){e.next=6;break}return t.editedItem.id_tipo=t.tipo_equipo,e.next=4,t.editTeam(t.editedItem);case 4:e.next=11;break;case 6:return t.editedItem.id_tipo=t.tipo_equipo,t.editedItem.bandera=t.editedItem.nombre,t.editedItem.escudo=t.editedItem.nombre,e.next=11,t.createTeam(t.editedItem);case 11:t.close();case 12:case"end":return e.stop()}}),e)})))()}})};const f=(0,r(900).Z)(d,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-container",[r("v-row",[r("v-col",{attrs:{cols:"12"}},[r("v-data-table",{staticClass:"elevation-1",attrs:{headers:t.headers,items:t.teams,"sort-by":"nombre",width:"100%"},scopedSlots:t._u([{key:"item.tipo",fn:function(e){var r=e.item;return[t._v("\n                    "+t._s(r.tipo.tipo)+"\n                ")]}},{key:"item.estado",fn:function(e){var n=e.item;return[r("v-chip",{attrs:{color:t.getColor(n.estado),dark:""}},[t._v("\n                    "+t._s(1==n.estado?"Habilitado":"Deshabilitado")+"\n                  ")])]}},{key:"top",fn:function(){return[r("v-toolbar",{attrs:{flat:""}},[r("v-toolbar-title",[t._v("Equipos")]),t._v(" "),r("v-divider",{staticClass:"mx-4",attrs:{inset:"",vertical:""}}),t._v(" "),r("v-spacer"),t._v(" "),r("v-dialog",{attrs:{"max-width":"500px"},scopedSlots:t._u([{key:"activator",fn:function(e){var n=e.on,o=e.attrs;return[r("v-btn",t._g(t._b({staticClass:"mb-2",attrs:{color:"primary",dark:""}},"v-btn",o,!1),n),[t._v("\n                            Nuevo\n                        ")])]}}]),model:{value:t.dialog,callback:function(e){t.dialog=e},expression:"dialog"}},[t._v(" "),r("v-card",[r("v-card-title",[r("span",{staticClass:"headline"},[t._v(t._s(t.formTitle))])]),t._v(" "),r("v-card-text",[r("v-container",[r("v-row",[r("v-col",{attrs:{cols:"12",sm:"6",md:"4"}},[r("v-text-field",{attrs:{label:"Nombre"},model:{value:t.editedItem.nombre,callback:function(e){t.$set(t.editedItem,"nombre",e)},expression:"editedItem.nombre"}})],1),t._v(" "),r("v-col",{attrs:{cols:"12",sm:"6",md:"4"}},[r("v-select",{attrs:{items:t.type_teams,"item-text":"nombre","item-value":"id",label:"Tipo","persistent-hint":"","single-line":""},model:{value:t.tipo_equipo,callback:function(e){t.tipo_equipo=e},expression:"tipo_equipo"}})],1)],1)],1)],1),t._v(" "),r("v-card-actions",[r("v-spacer"),t._v(" "),r("v-btn",{attrs:{color:"blue darken-1",text:""},on:{click:t.close}},[t._v("\n                            Cancelar\n                        ")]),t._v(" "),r("v-btn",{attrs:{color:"blue darken-1",text:""},on:{click:t.save}},[t._v("\n                            Guardar\n                        ")])],1)],1)],1),t._v(" "),r("v-dialog",{attrs:{"max-width":"60%"},model:{value:t.dialogDelete,callback:function(e){t.dialogDelete=e},expression:"dialogDelete"}},[r("v-card",[r("v-card-title",{staticClass:"headline"},[t._v('Esta por deshabilitar el eqiopo: "'+t._s(t.editedItem.nombre)+'", esta seguro?')]),t._v(" "),r("v-card-actions",[r("v-spacer"),t._v(" "),r("v-btn",{attrs:{color:"blue darken-1",text:""},on:{click:t.closeDelete}},[t._v("Cancel")]),t._v(" "),r("v-btn",{attrs:{color:"red darken-1",text:""},on:{click:t.deleteItemConfirm}},[t._v("Aceptar")]),t._v(" "),r("v-spacer")],1)],1)],1)],1)]},proxy:!0},{key:"item.actions",fn:function(e){var n=e.item;return[r("v-icon",{staticClass:"mr-2",attrs:{color:"blue darken-2"},on:{click:function(e){return t.editItem(n)}}},[t._v("\n                    mdi-pencil\n                ")]),t._v(" "),1==n.estado?r("v-icon",{staticClass:"mr-2",attrs:{color:"red darken-2"},on:{click:function(e){return t.deshabilitarItem(n)}}},[t._v("\n                    mdi-cancel\n                ")]):t._e(),t._v(" "),0==n.estado?r("v-icon",{staticClass:"mr-2",attrs:{color:"green darken-2"},on:{click:function(e){return t.habilitar(n)}}},[t._v("\n                    mdi-check\n                ")]):t._e()]}},{key:"no-data",fn:function(){return[r("v-btn",{attrs:{color:"primary"},on:{click:t.getCategories}},[t._v("\n                    Recargar\n                ")])]},proxy:!0}])})],1)],1)],1)}),[],!1,null,null,null).exports}}]);