<?php
include_once('init.php');

make_header("Flufcon", $language, "index");
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
<link rel='stylesheet' href='css/mapacz.css'>
<link rel="stylesheet" href="css/loader.css">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>
</head>

<body>
    <div id="menu">
        <div id="menu_logo">
            Flufcon<span id="span_logo">2025</span>
        </div>
        <div id="menu_timer">
            <?php
            $time = time();
            $target_date = strtotime("2025-01-21 19:00:00");
            $time_diff = $target_date - $time;
            $days = floor($time_diff / (60 * 60 * 24));
            $hours = floor(($time_diff % (60 * 60 * 24)) / (60 * 60));
            $minutes = floor(($time_diff % (60 * 60)) / 60);
            $seconds = $time_diff % 60;
            ?>
            <div id="days_div" class="number_div">
                <div class="count" id="days">
                    <?php echo $days; ?>
                </div>
                <div class="time_text">
                    dní
                </div>
            </div>
            <div class="doble_dot">
                :
            </div>
            <div id="hours_div" class="number_div">
                <div class="count" id="hours">
                    <?php echo $hours; ?>
                </div>
                <div class="time_text">
                    hodin
                </div>
            </div>
            <div class="doble_dot">
                :
            </div>
            <div id="minutes_div" class="number_div">
                <div class="count" id="minutes">
                    <?php echo $minutes; ?>
                </div>
                <div class="time_text">
                    minut
                </div>
            </div>
            <div class="doble_dot">
                :
            </div>
            <div id="seconds_div" class="number_div">
                <div class="count" id="seconds">
                    <?php echo $seconds; ?>
                </div>
                <div class="time_text">
                    sekund
                </div>
            </div>
        </div>
        <div id="menu_items">
            <div class="menu_item active" id="menu_item1">
                Hledám
            </div>
            <div class="menu_item" id="menu_item2">
                Nabízím
            </div>
        </div>
        <div class="halftone-wrapper">
            <div class="halftone">
                <img src="img/Tea3.jpg" class="menu_img">
            </div>
            <!-- <div class="halftone">
                <img src="img/green3.png" class="menu_img">
            </div> -->
        </div>
    </div>
    <script>
        time = <?php echo $time_diff; ?>;
        let days = document.getElementById("days");
        let hours = document.getElementById("hours");
        let minutes = document.getElementById("minutes");
        let seconds = document.getElementById("seconds");
        setInterval(function () {
            time--;
            let days = Math.floor(time / (60 * 60 * 24));
            let hours = Math.floor((time % (60 * 60 * 24)) / (60 * 60));
            let minutes = Math.floor((time % (60 * 60)) / 60);
            let seconds = time % 60;
            document.getElementById("days").innerHTML = days;
            document.getElementById("hours").innerHTML = hours;
            document.getElementById("minutes").innerHTML = minutes;
            document.getElementById("seconds").innerHTML = seconds;
        }, 1000);
    </script>
    <div id="map">
        <div id="mapa">
            <div id="mapa_img"></div>
            <div id="tlacitka_pozice">
            </div>
            <div id="control">
                <div class="zoom">
                    <div class="buttons">
                        <div class="button" id="minus_button">-</div>
                        <div class="button" id="white_line"></div>
                        <div class="button" id="plus_button">+</div>
                    </div>
                    <div class="priblizeni">
                        <div class="zoom_level" value=2>Svět</div>
                        <div class="zoom_level" value=5>Stát</div>
                        <div class="zoom_level" value=8>Kraj</div>
                        <div class="zoom_level" value=11>Město</div>
                        <div class="zoom_level" value=14>Obec</div>
                        <div class="zoom_level" value=18>Ulice</div>
                    </div>
                </div>
                <div class="compass" id="compass">
                </div>
                <script type="text/javascript">
                    var x = 17.2790949;
                    var y = 49.5963855;
                    var zoom = 11;
                    set_height();
                    var mapa = make_map(x, y, zoom);
                    // make_markers(domy, mapa);

                    var minus_button = document.getElementById("minus_button");
                    var plus_button = document.getElementById("plus_button");
                    var zoom_level = document.getElementsByClassName("zoom_level");
                    minus_button.addEventListener("click", function () {
                        mapa.zoomOut();
                    })
                    plus_button.addEventListener("click", function () {
                        mapa.zoomIn();
                    })
                    for (var i = 0; i < zoom_level.length; i++) {
                        var element = zoom_level[i];
                        element.addEventListener("click", function () {
                            var zoom = event.target.getAttribute("value");
                            zoom = parseInt(zoom);
                            mapa.setZoom(zoom);
                            console.log("zoom is:", zoom)
                        })
                    };

                    var compass = document.getElementById("compass");
                    let centerX = compass.offsetWidth / 2;
                    let centerY = compass.offsetHeight / 2;
                    compass.addEventListener('mousedown', handleTouchStart);
                    document.addEventListener('mousemove', handleMouseMove);
                    document.addEventListener('mouseup', handleMouseUp);
                    let isMousePressed = false;
                    var timeoutId;
                    mapa.addEventListener('click', function (e) {
                        var coord = e.latlng;
                        var lat = coord.lat;
                        var lng = coord.lng;
                        console.log("You clicked the map at latitude: " + lat + " and longitude: " + lng);
                        loading("response");
                        geokoduj_clik(lat, lng, mapa);
                    });
                </script>
            </div>
        </div>

        <div class="info" id="search_info">
            <form class="text" id="form_serach">
                <h1>
                    Hledáte odvoz?
                </h1>
                <div class="info_text div">
                    Vyhledejte své město kliknutím do mapy nebo ve vyhledávacím poli.
                </div>
                <div class="info_input div">
                    <input type="text" id="search" placeholder="Moje adresa">
                </div>
                <div class="info_button div">
                    <button id="search_button">Hledat</button>
                </div>
            </form>
            <div class="text" id="response">
                <!-- <h2>Kdo to má k vám nejblíže?</h2> -->
                <!-- <h1>
                    Kolem vás pojede:
                </h1>
                <div class="user">
                    <div class="name">
                        Soboli Ucho
                    </div>
                    <div class="time">
                        Vyjíždí 24. 1. 15:30
                    </div>
                    <div class=min_distance>
                        1 km
                    </div>
                    <div class="route">
                        <input type="button" id="ID_1" value="Zobrazit cestu">
                    </div>
                </div> -->
            </div>
        </div>
        <div class="info" id="offer_info">
            <div class="text" id="form_offer">
                <h1>
                    Máte místo v autě?
                </h1>
                <div class="info_text:">
                    Pomošte i ostatním kamošům.
                </div>
            </div>
            <div class="text" id="offer">
                <h1>
                    A ty jsi kdo?
                </h1>
                <form class="user_f" id="user_f">
                    <div class="name div">
                        <label for="in_name">Říkají mi</label>
                        <input type="text" id="in_name" placeholder="přezdívkou">
                    </div>
                    <div class="time div">
                        <label for="in_time">Vyjíždím</label>
                        <input type="datetime-local" id="in_time" value="2025-01-25T17:00">
                    </div>
                    <div class="route div">
                        <label for="in_route">Z</label>
                        <input type="text" id="in_route" placeholder="města">
                    </div>
                    <div class="road div">
                        <label for="dálnice">Jedu přes dálnice</label>
                        <input type="checkbox" id="dalnice">
                    </div>
                    <div class="through div" id="through">
                        <label for="in_through">Přes</label>
                        <input type="text" class="in_through" placeholder="další města">
                    </div>
                    <div class="fp_button div">
                        <input type="button" value="Přidat město" id="add_city">
                    </div>
                    <div class="f_button div">
                        <input type="submit" value="Pojeďte semnou">
                    </div>
                </form>
            </div>
            <div id="wait">

            </div>
        </div>
    </div>

    <script>
        document.getElementById("form_serach").addEventListener("submit", function (event) {
            event.preventDefault();
            let search = document.getElementById("search").value;
            console.log("search is:", search);
            loading("response");
            removeRoute();
            document.getElementById("response").style.display = "block";
            geokoduj(search, mapa);
        })
        let menu_item1 = document.getElementById("menu_item1");
        let menu_item2 = document.getElementById("menu_item2");
        menu_item1.addEventListener("click", function () {
            menu_item1.classList.add("active");
            menu_item2.classList.remove("active");
            document.getElementById("search_info").style.display = "flex";
            document.getElementById("offer_info").style.display = "none";
        })
        menu_item2.addEventListener("click", function () {
            menu_item2.classList.add("active");
            menu_item1.classList.remove("active");
            document.getElementById("search_info").style.display = "none";
            document.getElementById("offer_info").style.display = "flex";
        })
        document.getElementById("user_f").addEventListener("submit", function (event) {
            event.preventDefault();
            loading("wait");
            removeRoute();
            let name = document.getElementById("in_name").value;
            let time = document.getElementById("in_time").value;
            let route = document.getElementById("in_route").value;
            let dalnice = !document.getElementById("dalnice").checked;
            let through = document.getElementsByClassName("in_through");
            let throughs = [];
            for (var i = 0; i < through.length; i++) {
                if (through[i].value != "") { throughs.push(through[i].value) };
            }
            console.log("name is:", name);
            console.log("time is:", time);
            console.log("route is:", route);
            console.log("dalnice is:", dalnice);
            console.log("through is:", throughs);
            loading("response");
            send_offer(name, time, route, dalnice, throughs);
        })
        document.getElementById("add_city").addEventListener("click", function () {
            let through = document.createElement("input");
            through.classList.add("in_through");
            through.placeholder = "další města";
            document.getElementById("through").appendChild(through);
        })
    </script>
    <a href="https://soboliucho.cz" id="su_logo"><img src="img/ikona_white.png" alt="Power by SU" id="su_logo_img"></a>

</body>

</html>