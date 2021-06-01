<script>
    var nav_1_elements = [
            {
                id: 'nav_1_usuarios',
                text: 'Usuarios',
                active: false,
                style: '',
                icon: 'fa fa-fw fa-user',
                cf: 'users/explore',
                submenu: false,
                subelements: []
            },
            {
                id: 'nav_1_noticias',
                text: 'Noticias',
                active: false,
                style: '',
                icon: 'fa fa-fw fa-newspaper',
                cf: 'noticias/explore',
                submenu: false,
                subelements: []
            },
            {
                id: 'nav_1_albums',
                text: 'Álbums',
                active: false,
                style: '',
                icon: 'fa fa-fw fa-images',
                cf: 'albums/explore',
                submenu: false,
                subelements: []
            },
            {
                id: 'nav_1_comercial',
                text: 'Comercial',
                active: false,
                style: '',
                icon: 'fa fa-fw fa-shopping-cart',
                cf: '',
                submenu: true,
                subelements: [
                    {
                        text: 'Productos',
                        active: false,
                        icon: 'fa fa-fw fa-book',
                        cf: 'products/explore'
                    },
                    {
                        text: 'Órdenes',
                        active: false,
                        icon: 'fa fa-fw fa-shopping-cart',
                        cf: 'orders/explore'
                    }
                ]
            },
            {
                id: 'nav_1_files',
                text: 'Archivos',
                active: false,
                style: '',
                icon: 'fa fa-fw fa-file-alt',
                cf: 'files/explore',
                submenu: false,
                subelements: []
            },
            {
                id: 'nav_1_estadisticas',
                text: 'Estadísticas',
                active: false,
                style: '',
                icon: 'fa fa-fw fa-chart-bar',
                cf: '',
                submenu: true,
                subelements: [
                    {
                        text: 'Visitas Girls',
                        active: false,
                        icon: 'fa fa-fw fa-user',
                        cf: 'statistics/girls'
                    },
                    {
                        text: 'Visitas Álbums',
                        active: false,
                        icon: 'fa fa-fw fa-image',
                        cf: 'statistics/albums'
                    },
                    {
                        text: 'Usuarios',
                        active: false,
                        icon: 'fa fa-fw fa-user',
                        cf: 'statistics/users'
                    }
                ]
            },
            {
                id: 'nav_1_ajustes',
                text: 'Ajustes',
                active: false,
                style: '',
                icon: 'fa fa-fw fa-sliders-h',
                cf: '',
                submenu: true,
                subelements: [
                    {
                        text: 'General',
                        active: false,
                        icon: 'fa fa-fw fa-cogs',
                        cf: 'admin/acl'
                    },
                    {
                        text: 'Ítems',
                        active: false,
                        icon: 'fa fa-fw fa-bars',
                        cf: 'items/manage'
                    },
                    {
                        text: 'Posts',
                        active: false,
                        icon: 'fa fa-fw fa-bars',
                        cf: 'posts/explore'
                    },
                    {
                        text: 'Base de datos',
                        active: false,
                        icon: 'fa fa-fw fa-database',
                        cf: 'sync/panel'
                    },
                    {
                        text: 'Eventos',
                        active: false,
                        icon: 'fa fa-fw fa-calendar',
                        cf: 'events/explore'
                    }
                ]
            }
        ];
</script>