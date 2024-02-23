<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <title>Smart School!</title>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .marquee {
            position: relative;
            width: 100vw;
            max-width: 100%;
            height: 7vh;
            overflow-x: hidden;
        }

        .track {
            display: flex;
            flex-direction: row;
            position: absolute;
            white-space: nowrap;
            will-change: transform;
            animation: marquee 25s linear infinite;
        }

        .track div {
            font-size: 4vh;
            color: whitesmoke;
        }

        body {
            height: 90vh;

        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background-color: #007BA7;
            color: white;
            font-size: 3rem;
            font-weight: bold;
        }

        #viewTitle {
            background-color: rgb(33, 37, 41);
            color: white;
            padding: 5px;
            font-size: 2.5rem;
            font-weight: bold;
            padding-left: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .content {
            display: inline-block;
            padding: 0 2vw;
        }

        @keyframes marquee {
            from {
                transform: translateX(0%);
            }

            to {
                transform: translateX(-50%);
            }
        }

        span {
            font-weight: bold;
        }
    </style>
</head>

<body>


    <header>
        <div>
            Projecto: Smart Eco School
        </div>
        <div style="font-weight: bold;" id="clock"></div>
    </header>
    <div id="viewTitle"></div>

    <main class="container-fluid ">
        @yield('content')
    </main>

    <nav class="navbar fixed-bottom navbar-dark bg-dark">
        <div class="marquee">
            <div class="track">
                <div id=scrollText class="content"></div>
                <div id=scrollTextCopy class="content"></div>
            </div>
        </div>
    </nav>

</body>
<script>
    //Añadir consejos
    let consejos = [
        "     Consejo 1: Lorem ipsum much texdo dolor sator arepo tenet opera rotas este es un cosejo poromocionado por el cabildo de gran canaria y financiado con vuestros impuestos jiji jaja money money me encanta el dinero &nbsp;	&nbsp;",
        "     Consejo 2: Lorem ipsum much texdo dolor tenet arepo opera rotas este es un cosejo y es mas corto	&nbsp;	&nbsp;",
        "     Consejo 3: Lorem ipsum much texdo dolor tenet arepo opera rotas este es un cosejo poromocionado por el cabildo de gran canaria y financiado con vuestros impuestos jiji jaja money money me encanta el dinero y ademas soy un texto mucho mas largo que el resto, o mai got que texto tn largo madre mi aue alguien llame a los bomberos no queda espacio en este disco duro para tantos caracteres y se mueve demasiado largo sefuramente habra que hacwer algo con js para que no vaya tan rapido o mai &nbsp;	&nbsp;",
        "     Consejo 4: Lorem ipsum much texdo dolor tenet arepo opera rotas este es un cosejo poromocionado por el cabildo de gran y soy de tamaño mediano &nbsp;	&nbsp;",
    ]
    changeText();

    function randomInteger(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function changeText() {
        let scroll = document.getElementById("scrollText")
        let scrollCopy = document.getElementById("scrollTextCopy")

        scrollText = consejos[randomInteger(0, consejos.length - 1)]
        changeScrollSpeed(scrollText.length);

        scroll.innerHTML = scrollText
        scrollCopy.innerHTML = scrollText
    }

    function changeScrollSpeed(length) {
        const scrollSpeed = 10;

        //Calculate time needed to scroll text at scrollSpeed characters per second
        time = length / scrollSpeed;
        //change css values
        document.querySelector(".track").style.animationDuration = time + "s";


    }
</script>

</html>