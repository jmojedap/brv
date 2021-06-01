<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- PML Tools -->
    <link type="text/css" rel="stylesheet" href="<?= URL_RESOURCES . 'css/style_pml.css' ?>">
    <script src="<?= URL_RESOURCES . 'js/pcrn.js' ?>"></script>

    <!-- Vue.js -->
    <?php $this->load->view('assets/vue') ?>



    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/7.20.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.20.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.20.0/firebase-firestore.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
        https://firebase.google.com/docs/web/setup#available-libraries -->
    <script src="https://www.gstatic.com/firebasejs/7.20.0/firebase-analytics.js"></script>

    <script>
    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    var firebaseConfig = {
        apiKey: "AIzaSyBcqP8nbLbWuSCqCsOstybGOjKw2idHqfo",
        authDomain: "vebonit-b4f3d.firebaseapp.com",
        databaseURL: "https://vebonit-b4f3d.firebaseio.com",
        projectId: "vebonit-b4f3d",
        storageBucket: "vebonit-b4f3d.appspot.com",
        messagingSenderId: "912806088346",
        appId: "1:912806088346:web:4c1b5f85f3fc0f4f09d923",
        measurementId: "G-3FWVYN4WV4"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    firebase.analytics();
    </script>

    <script src="<?= URL_RESOURCES . 'firebase/app_auth.js' ?>"></script>

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Pacarina</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pointer" onclick="appSingOut()">Salir</span></a>
                    </li>
                </ul>
                
            </div>
        </div>
    </nav>
    <div class="container pt-2">
        <div id="auth_app">
            <div class="card mb-2 center_box_450">
                <div class="card-body">
                    <h3>Registro</h3>
                    <form accept-charset="utf-8" method="POST" id="signup_form" @submit.prevent="send_form">
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-right">E-mail</label>
                            <div class="col-md-8">
                                <input name="email" type="email" class="form-control" required title="E-mail"
                                    placeholder="E-mail" v-model="form_values.email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-right">Contraseña</label>
                            <div class="col-md-8">
                                <input name="password" type="text" class="form-control" required title="contraseña"
                                    placeholder="contraseña" v-model="form_values.password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-success btn-block" type="submit">
                                    Registrarme
                                </button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-light btn-block" onclick="googleSignIn()" type="button">
                                    Sign In with Google
                                </button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary btn-block" onclick="facebookSignIn()" type="button">
                                    Sign In with Facebook
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card center_box_450">
                <div class="card-body">
                    <h3>Ingresar</h3>
                    <form accept-charset="utf-8" method="POST" @submit.prevent="send_signin">
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-right">E-mail</label>
                            <div class="col-md-8">
                                <input name="email" type="email" class="form-control" required title="E-mail"
                                    placeholder="E-mail" v-model="signin.email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-right">Contraseña</label>
                            <div class="col-md-8">
                                <input name="password" type="text" class="form-control" required title="contraseña"
                                    placeholder="contraseña" v-model="signin.password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-success btn-block" type="submit">
                                    Ingresar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    new Vue({
        el: '#auth_app',
        created: function() {
            //this.get_list();
        },
        data: {
            dato: 'mundo vue.js',
            user: {
                firebase_id: '',
                photo_url: '',
                display_name: ''
            },
            form_values: {
                email: 'jmojedap@gmail.com',
                password: 'probandofirebase123'
            },
            signin: {
                email: 'jmojedap@gmail.com',
                password: 'probandofirebase123'
            },

        },
        methods: {
            facebook_auth: function() {
                firebase.auth().signInWithPopup(provider).then(function(result) {
                    console.log('iniciando');
                    // This gives you a Facebook Access Token. You can use it to access the Facebook API.
                    var token = result.credential.accessToken;
                    // The signed-in user info.
                    var user = result.user;

                    console.log(result);
                    /*user.forEach(element => {
                        console.log('elemento');
                        console.log(element);
                    });*/
                    console.log(typeof user);


                    /*this.user.firebase_id = user.uid;
                    this.user.photo_url = user.photoURL;
                    this.user.display_name = user.displayName;*/


                    console.log('modificado');
                    // ...
                }).catch(function(error) {
                    // Handle Errors here.
                    var errorCode = error.code;
                    var errorMessage = error.message;
                    // The email of the user's account used.
                    var email = error.email;
                    // The firebase.auth.AuthCredential type that was used.
                    var credential = error.credential;
                    // ...
                });
            },
            send_form: function() {
                console.log('hola a todos');
                auth.createUserWithEmailAndPassword(this.form_values.email, this.form_values.password)
                    .then(userCredential => {
                        console.log('signup');
                    })
                    .catch();
            },
            send_signin: function() {
                auth.signInWithEmailAndPassword(this.signin.email, this.signin.password)
                    .then(userCredential => {
                        console.log('signup');
                    })
                    .catch();
            },
        }
    });
</script>
    </div>
</body>

</html>