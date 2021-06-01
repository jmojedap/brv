<aside class="main-sidebar" id="nav_1">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li v-for="(element, i) in elements" v-bind:class="{ treeview: element.subelements.length, 'active': element.active }">
                <a href="#" v-on:click="nav_1_click(i)">
                    <i class="fa-fw" v-bind:class="element.icon"></i>
                    {{ element.text }}
                    <i class="fa fa-angle-left float-right" v-if="element.subelements.length > 0"></i>
                </a>
                <ul class="treeview-menu" v-if="element.subelements.length > 0">
                    <li v-for="(subelement, j) in element.subelements" v-bind:class="{ 'active': subelement.active }">
                        <a href="#" v-on:click="nav_1_click_sub(i,j)">
                            <i class="fa-fw" v-bind:class="subelement.icon"></i>
                            {{ subelement.text }}
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
</aside>

<script>
//Activación inicial de elementos actuales
//-----------------------------------------------------------------------------
    nav_1_elements.forEach(element => {
        //Activar elemento actual, si está en las secciones
        if ( element.sections.includes(app_cf) ) { element.active = true }
        //Activar subelemento actual, si está en las secciones
        if ( element.subelements )
        {
            element.subelements.forEach(subelement => {
                if ( subelement.sections.includes(app_cf) )
                {
                    element.active = true
                    subelement.active = true
                }
            })
        }
    });

// VueApp nav_1
//-----------------------------------------------------------------------------
new Vue({
    el: '#nav_1',
    data: {
        elements: nav_1_elements
    },
    methods: {
        nav_1_click: function(i){
            if ( this.elements[i].subelements.length == 0 )
            {
                if ( this.elements[i].anchor ) {
                    window.location = url_admin + this.elements[i].cf;
                } else {
                    this.elements.forEach(element => { element.active = false; });
                    this.elements[i].active = true;
                    $('.treeview-menu').slideUp();
                    app_cf = this.elements[i].cf;
                    load_sections('nav_1')
                    this.hide_sidebar()
                }
            }
        },
        nav_1_click_sub: function(i,j){
            if ( this.elements[i].subelements[j].anchor ) {
                window.location = url_admin + this.elements[i].subelements[j].cf;
            } else {
                //Activando elemento
                this.elements.forEach(element => { element.active = false; });
                this.elements[i].active = true;
    
                //Activando subelemento
                this.elements[i].subelements.forEach(subelement => { subelement.active = false; });
                this.elements[i].subelements[j].active = true;
    
                //Cargando secciones
                app_cf = this.elements[i].subelements[j].cf;
                load_sections('nav_1')
                this.hide_sidebar()
            }
        },
        hide_sidebar: function(){
            setTimeout(() => {
                //Enable hide menu when clicking on the content-wrapper on small screens
                if ($(window).width() <= 767 && $("body").hasClass("sidebar-open")) {
                    $("body").removeClass('sidebar-open');
                }
            }, 150);
        },
    }
});
</script>