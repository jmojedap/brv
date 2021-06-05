var nav_1_elements = [
    {
        text: 'Inicio', active: false, icon: 'fa fa-home', cf: 'app/dashboard', anchor: false,
        sections: ['app/dashboard'],
        subelements: []
    },
    {
        text: 'Usuarios', active: false, icon: 'fa fa-user', cf: 'users/explore', anchor: false,
        sections: ['users/explore', 'users/add', 'users/import', 'users/profile', 'users/edit', 'users/assigned_contents'],
        subelements: []
    },
    {
        text: 'Publicaciones',
        active: false,
        icon: 'far fa-file-alt',
        cf: 'posts/explore',
        sections: ['posts/explore', 'posts/add', 'posts/import', 'posts/info', 'posts/edit', 'posts/image', 'posts/details', 'posts/comments'],
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
        text: 'Datos',
        active: false,
        style: '',
        icon: 'fa fa-bars',
        cf: '',
        sections: [],
        subelements: [
            {
                text: 'Archivos', active: false, icon: 'far fa-image', cf: 'files/explore',
                sections: ['files/explore', 'files/add', 'files/import', 'files/info', 'files/edit', 'files/image', 'files/details'],
            },
            {
                text: 'Eventos', active: false, icon: 'far fa-clock', cf: 'events/summary', anchor: false,
                sections: ['events/explore', 'events/summary']
            },
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
                text: 'Lugares', active: false, icon: 'fa fa-map-marker-alt', cf: 'places/explore', anchor: false,
                sections: ['places/explore', 'places/add', 'places/edit'],
            }
        ]
    }
];