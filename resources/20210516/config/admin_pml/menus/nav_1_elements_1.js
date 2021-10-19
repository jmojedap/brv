var nav_1_elements = [
    {
        text: 'Inicio', active: false, icon: 'fa fa-home', cf: 'app/dashboard', anchor: false,
        sections: ['app/dashboard'],
        subelements: []
    },
    {
        text: 'Usuarios', active: false, icon: 'fa fa-user', cf: 'users/explore', anchor: false,
        sections: ['users/explore', 'users/add', 'users/import', 'users/profile', 'users/edit', 'users/inbody'],
        subelements: []
    },
    {
        text: 'Actividad',
        active: false,
        icon: 'fa fa-running',
        cf: '',
        sections: [],
        subelements: [
            {
                text: 'InBody',
                active: false,
                icon: 'fa fa-weight',
                cf: 'inbody/explore',
                sections: ['inbody/explore', 'inbody/import', 'inbody/import_e']
            },
        ]
    },
    {
        text: 'Posts',
        active: false,
        icon: 'far fa-file-alt',
        cf: 'posts/explore',
        sections: ['posts/explore', 'posts/add', 'posts/import', 'posts/info', 'posts/edit', 'posts/image', 'posts/details', 'posts/comments'],
        subelements: []
    },
    {
        text: 'Archivos',
        active: false,
        icon: 'far fa-image',
        cf: 'files/explore',
        sections: ['files/explore', 'files/add', 'files/import', 'files/info', 'files/edit', 'files/image', 'files/details'],
        subelements: []
    },
    {
        text: 'Calendario',
        active: false,
        icon: 'far fa-calendar',
        cf: 'calendar/calendar',
        sections: ['calendar/calendar', 'calendar/programacion_automatica'],
        subelements: []
    },
    {
        text: 'Comentarios',
        active: false,
        icon: 'far fa-comment',
        cf: 'comments/explore',
        sections: ['comments/explore', 'comments/add', 'comments/info'],
        subelements: []
    },
    {
        text: 'Comercial',
        active: false,
        icon: 'fa fa-shopping-cart',
        cf: '',
        sections: [],
        subelements: [
            {
                text: 'Compras',
                active: false,
                icon: 'fa fa-shopping-cart',
                cf: 'orders/explore',
                sections: ['orders/explore', 'orders/info']
            },
            {
                text: 'Products',
                active: false,
                icon: 'fa fa-book',
                cf: 'products/explore',
                sections: ['products/explore', 'products/import', 'products/add', 'products/edit', 'products/edit', 'products/posts', 'products/info']
            }
        ]
    },
    {
        text: 'Ajustes',
        active: false,
        style: '',
        icon: 'fa fa-sliders-h',
        cf: '',
        sections: ['config/options'],
        subelements: [
            {
                text: 'General', active: false, icon: 'fa fa-cogs', cf: 'config/options',
                sections: ['config/options']
            },
            {
                text: 'Ítems', active: false, icon: 'fa fa-bars', cf: 'items/manage',
                sections: ['items/manage', 'items/import']
            },
            {
                text: 'Base de datos', active: false, icon: 'fa fa-database', cf: 'sync/panel',
                sections: ['sync/panel']
            },
            {
                text: 'Eventos', active: false, icon: 'far fa-clock', cf: 'events/summary', anchor: false,
                sections: ['events/explore', 'events/summary']
            },
            {
                text: 'Lugares', active: false, icon: 'fa fa-map-marker-alt', cf: 'places/explore', anchor: false,
                sections: ['places/explore', 'places/add', 'places/edit'],
            }
        ]
    },
    {
        text: 'Ayuda',
        active: false,
        icon: 'far fa-question-circle',
        cf: 'app/help',
        sections: ['app/help'],
        subelements: []
    },
];