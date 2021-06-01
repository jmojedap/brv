<div id="nav_3_vue" class="mb-2">
    <div class="only-lg">
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item" v-for="(element, key) in nav_3">
                <a
                    class="nav-link"
                    href="#"
                    v-bind:class="element.class"
                    v-on:click="activate_menu(key)"
                >
                    <i v-bind:class="element.icon"></i>
                    {{ element.text }}
                </a>
            </li>
        </ul>
    </div>

    <div class="dropdown only-sm">
        <button class="btn btn-light btn-block " type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ current.text }}
            <span class="float-right">
                <i class="fa fa-chevron-down"></i>
            </span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="width: 100%">
            <button class="dropdown-item" type="button"
                v-for="(element, key) in nav_3"
                v-bind:class="element.class"
                v-on:click="activate_menu(key)"
                >
                <i class="w30p" v-bind:class="element.icon"></i>
                {{ element.text }}
            </button>
            
        </div>
    </div>
</div>

<script>
var nav_3_vue = new Vue({
    el: '#nav_3_vue',
    created: function(){
        this.set_current();
    },
    data: {
        nav_3: nav_3,  //Elementos contenido del menú
        current: { text: 'Menú' }
    },
    methods: {
        activate_menu: function (key) {
            this.current = this.nav_3[key];
            for ( i in this.nav_3 ) this.nav_3[i].class = ''
            
            this.nav_3[key].class = 'active'   //Elemento actual
            if ( this.nav_3[key].anchor ) {
                window.location = url_app + this.nav_3[key].cf
            } else {
                this.load_view_a(key)
            }
        },
        load_view_a: function(key){
            app_cf = this.nav_3[key].cf
            //console.log(app_cf);
            load_sections('nav_3') //Función global
        },
        set_current: function(){
            for ( i in this.nav_3 ){
                if ( this.nav_3[i].class == 'active' ) {
                    this.current = this.nav_3[i]
                }
            }
        }
    }
});
</script>