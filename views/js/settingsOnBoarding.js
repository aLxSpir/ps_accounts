(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["settingsOnBoarding"],{"036b":function(t,o,n){var e=n("24fb");o=e(!1),o.push([t.i,"section[data-v-c01c0900]{margin-bottom:35px}",""]),t.exports=o},"0e0f":function(t,o,n){"use strict";n("8564")},"0ff7":function(t,o,n){"use strict";n.r(o);var e=function(){var t=this,o=t.$createElement,n=t._self._c||o;return n("div",{staticClass:"pt-5"},[n("section",[n("ConfigInformation",{attrs:{app:t.app}})],1),n("section",[n("div",{staticClass:"m-auto p-0 container"},[n("PsAccounts",{attrs:{"force-show-plans":!0}})],1)])])},i=[],s=(n("b64b"),n("5530")),r=function(){var t=this,o=t.$createElement,e=t._self._c||o;return e("b-container",{staticClass:"m-auto p-0",attrs:{id:"config-information"}},[e("div",{staticClass:"d-flex col-left"},[e("b-col",{staticClass:"p-16 m-auto text-center d-flex",attrs:{sm:"4",md:"4",lg:"4"}},[e("img",{attrs:{src:n("3875"),width:"50",height:"50"}}),e("h1",[e("span",{staticClass:"white-text"},[t._v("Prestashop")]),t._v(" Account")])]),e("b-col",{staticClass:"col-right py-5",attrs:{sm:"8",md:"8",lg:"8"}},[e("h2",[t._v(t._s(t.$t("configure.incentivePanel.howTo")))])])],1)])},c=[],a={name:"ConfigInformation",props:["app","configurationPage"],methods:{startSetup:function(){window.open(this.configurationPage)}}},f=a,l=(n("17b4"),n("2877")),p=Object(l["a"])(f,r,c,!1,null,null,null),g=p.exports,u=n("a85d"),d=n("cebc"),h={components:{PsAccounts:u["PsAccounts"],ConfigInformation:g},methods:Object(s["a"])({},Object(d["c"])({getListProperty:"getListProperty"})),data:function(){return{loading:!0,unwatch:""}},created:function(){var t=this;this.googleLinked&&(this.loading=!0,this.getListProperty()),this.unwatch=this.$store.watch((function(t,o){return{googleLinked:t.settings.googleLinked,countProperty:t.settings.countProperty,listProperty:t.settings.state.listPropertySuccess}}),(function(o){o.googleLinked&&Object.keys(o.listProperty).length<o.countProperty&&t.getListProperty(),Object.keys(o.listProperty).length>=o.countProperty&&(t.loading=!1)}),{immediate:!0})},beforeDestroy:function(){this.unwatch()},computed:{app:function(){return this.$store.state.app.app},connectedAccount:function(){return this.$store.state.settings.connectedAccount}}},m=h,b=(n("0e0f"),Object(l["a"])(m,e,i,!1,null,"c01c0900",null));o["default"]=b.exports},"17b4":function(t,o,n){"use strict";n("cfb7")},3875:function(t,o,n){t.exports=n.p+"img/prestashop-logo.png"},8564:function(t,o,n){var e=n("036b");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var i=n("499e").default;i("fad89efa",e,!0,{sourceMap:!1,shadowMode:!1})},"98b9":function(t,o,n){var e=n("24fb");o=e(!1),o.push([t.i,"#config-information{background-color:#011638;border-radius:.25rem}#config-information .col-left>.d-flex{align-items:center;justify-content:flex-start;padding-left:4rem}#config-information .col-left>.d-flex h1{font-size:18px;color:#6b868f;font-weight:300;margin-top:0;margin-bottom:0;margin-left:.5rem;margin-right:.5rem}#config-information .col-left>.d-flex h1 span.white-text{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity));font-weight:700}#config-information .col-left>.d-flex img{max-width:100%}#config-information div.col-right{display:flex;justify-content:flex-end;padding-right:4rem}#config-information div.col-right h2{font-size:22px;max-width:50%;--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity));font-weight:700;margin-bottom:0}",""]),t.exports=o},cfb7:function(t,o,n){var e=n("98b9");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var i=n("499e").default;i("021252a4",e,!0,{sourceMap:!1,shadowMode:!1})}}]);