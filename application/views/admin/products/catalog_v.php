<div id="app_catalog" class="catalog">
    <form accept-charset="utf-8" method="POST" id="search_form" @submit.prevent="get_list">
        <div class="row mb-2">
            <div class="col-md-12">
                <input
                    name="q" id="field-q" type="text" class="form-control" required
                    title="Buscar libro" placeholder="Buscar..."
                >
            </div>
        </div>
    </form>

    <div class="mb-2">
        <button class="btn btn-light" v-on:click="sum_page(-1)" title="Página anterior">
            <i class="fa fa-caret-left"></i> Anterior
        </button>
        <button class="btn btn-light float-right" v-on:click="sum_page(1)" title="Página siguiente">
            Siguiente <i class="fa fa-caret-right"></i>
        </button>
    </div>

    <table class="table">
        <tbody>
            <tr v-for="(book, book_key) in list" class="product">
                <td width="120px">
                    <a v-bind:href="`<?= URL_ADMIN . "products/details/" ?>` + book.id + `/` + book.slug" class="">
                        <img
                            v-bind:src="`<?= URL_CONTENT ?>books/covers/` + book.slug + `.jpg`"
                            class="product_img"
                            alt="cubierta libro"
                            onerror="this.src='<?= URL_IMG ?>books/md_nd.png'"
                        >   
                    </a>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "products/details/" ?>` + book.id" class="product_title">
                        {{ book.name }}
                    </a>
                    <p class="text-muted">{{ book.text_1 }}</p>

                    <p class="product_price mb-2">{{ book.price | currency }}</p>
                    <button class="btn btn-main btn-sm" v-on:click="check_external_url(book_key)" v-if="user_id > 0">
                        Comprar
                    </button>
                    <button class="btn btn-main btn-sm" data-toggle="modal" data-target="#modal_signup" v-else>
                        Comprar
                    </button>
                </td>
            </tr>
        </tbody>
    </table>

    <?php $this->load->view('accounts/modal_signup_v') ?>
</div>

<script>
// Filters
//-----------------------------------------------------------------------------
Vue.filter('currency', function (value) {
    if (!value) return '';
    value = '$ ' + new Intl.NumberFormat().format(value);
    return value;
});

// Vue App
//-----------------------------------------------------------------------------
var app_catalog = new Vue({
    el: '#app_catalog',
    created: function(){
        this.get_list();
    },
    data: {
        cf: 'products/catalog/',
        controller: 'products',
        product_family: '<?= $product_family ?>',
        user_id: '<?= $this->session->userdata('user_id') ?>',
        book_id: 0,
        list: [],
        num_page: <?= $num_page ?>,
        max_page: 1,
    },
    methods: {
        get_list: function(){
            axios.post(url_app + this.controller + '/get_catalog/' + this.product_family + '/' + this.num_page, $('#search_form').serialize())
            .then(response => {
                this.list = response.data.list;
                this.max_page = response.data.max_page;
                //$('#head_subtitle').html(response.data.search_num_rows);
                history.pushState(null, null, url_app + this.cf + this.product_family + '/' + this.num_page +'/?' + response.data.str_filters);
                this.all_selected = false;
                this.selected = [];
            })
            .catch(function (error) { console.log(error) })
        },
        check_external_url: function(book_key){
            var external_url = this.list[book_key].external_url;
            if ( external_url.length == 0 ) {
                this.add_product(book_key);
            } else {
                window.open(external_url, '_blank');
            }
        },
        add_product: function(book_key){
            this.book_id = this.list[book_key].id;
            axios.get(url_app + 'orders/add_product/' + this.book_id)
            .then(response => {
                if ( response.data.status == 1 ) {
                    window.location = url_app + 'orders/checkout';
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        sum_page: function(sum){
            this.num_page = Pcrn.limit_between(this.num_page + sum, 1, this.max_page);
            this.get_list();
        }
    }
});
</script>