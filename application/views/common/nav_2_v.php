<div id="nav_2_vue" class="mb-2">
    <div class="only-lg">
        <ul class="nav nav-tabs nav-tabs-line" role="tablist">
            <?php if ( isset($back_link) ) : ?>
                <li class="nav-item">
                    <a href="<?= $back_link ?>" class="nav-link"> 
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item" v-for="(element, key) in nav_2">
                <a
                    class="nav-link" href="#"
                    v-bind:class="element.class"
                    v-on:click="activate_menu(key)"
                >
                    {{ element.text }}
                </a>
            </li>
        </ul>
    </div>

    <div class="dropdown only-sm">
        <div class="d-flex">
            <?php if ( isset($back_link) ) : ?>
                <div class="mr-2">
                    <a href="<?= $back_link ?>" class="btn-circle"> 
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </div>
            <?php endif; ?>
            <h2 class="nav_2_title">{{ current.text }}</h2>
            <div class="ml-auto">
                <a class="btn-circle" role="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="float-right">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <button class="dropdown-item" type="button"
                        v-for="(element, key) in nav_2"
                        v-bind:class="element.class"
                        v-on:click="activate_menu(key)"
                        >
                        {{ element.text }}
                    </button>
                    
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    new Vue({
        el: '#nav_2_vue',
        created: function(){
            this.set_current();
        },
        data: {
            nav_2: nav_2,  //Elementos contenido del menú
            current: { text: 'Menú' }
        },
        methods: {
            activate_menu: function (key) {
                this.current = this.nav_2[key];
                for ( i in this.nav_2 ){
                    this.nav_2[i].class = '';
                }
                this.nav_2[key].class = 'active';   //Elemento actual
                if ( this.nav_2[key].anchor ) {
                    window.location = url_app + this.nav_2[key].cf;
                } else {
                    this.load_view_a(key);
                }
            },
            load_view_a: function(key){
                app_cf = this.nav_2[key].cf;
                //console.log(app_cf);
                load_sections('nav_2'); //Función global
            },
            set_current: function(){
                for ( i in this.nav_2 ){
                    if ( this.nav_2[i].class == 'active' ) {
                        this.current = this.nav_2[i];
                    }
                }
            }
        }
    });
</script>