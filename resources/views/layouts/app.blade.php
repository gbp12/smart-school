<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <title>Smart School!</title>
    <style>
        .marquee {
            position: relative;
            width: 100vw;
            max-width: 100%;
            height: 200px;
            overflow-x: hidden;
        }

        #scrollText {
            font-size: 40px;
        }

        .track {
            position: absolute;
            white-space: nowrap;
            will-change: transform;
            animation: marquee 5s linear infinite;
        }

        @keyframes marquee {
            from {
                transform: translateX(60%);
            }

            to {
                transform: translateX(-100%);
            }
        }
    </style>
</head>

<body>

    <header> este es el header de la app </header>
    @yield('content')
    <div class="marquee">
        <div class="track">
            <div id=scrollText class="content">&nbsp;Test</div>
        </div>
    </div>

</body>
<script>
    let consejos = [
        "Consejo 1: Lorem ipsum much texdo dolor tenet arepo opera rotas este es un cosejo poromocionado por el cabildo de gran canaria y financiado con vuestros impuestos jiji jaja money money me encanta el dinero",
        "Consejo 2: Lorem ipsum much texdo dolor tenet arepo opera rotas este es un cosejo poromocionado por el cabildo de gran canaria y financiado con vuestros impuestos jiji jaja money money me encanta el dinero",
        "Consejo 3: Lorem ipsum much texdo dolor tenet arepo opera rotas este es un cosejo poromocionado por el cabildo de gran canaria y financiado con vuestros impuestos jiji jaja money money me encanta el dinero",
        "Consejo 4: Lorem ipsum much texdo dolor tenet arepo opera rotas este es un cosejo poromocionado por el cabildo de gran canaria y financiado con vuestros impuestos jiji jaja money money me encanta el dinero",
    ]
    changeText();

    function randomInteger(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function changeText() {
        let scroll = document.getElementById("scrollText")
        //num = randomInteger(0, 3)
        //newText = consejos[num]
        //console.log(newText + " y num = ", num);
        scroll.innerHTML = consejos[randomInteger(0, 3)]
    }
</script>

</html>