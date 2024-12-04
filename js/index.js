const API_KEY = 'js-yzrfuV4oz_pHbd8c5rY4kmL3dxyVtsg5lGzBO-bE';

function set_height() {
    const win_height = window.innerHeight;
    var nelista = document.getElementById("map");
    var lista = document.getElementById("menu");
    // window.addEventListener('load', function () {
    const size_lista = lista.clientHeight;
    var size = (win_height - size_lista) - 2 - 16;
    // console.log("size map: ", size);
    // console.log("size lista: ", size_lista);
    // console.log("win_height: ", win_height);
    nelista.style.height = `${size}px`
    // });
}

function make_map(x, y, zoom) {

    var map = L.map('mapa_img', { zoomControl: false }).setView([y, x], zoom);
    L.tileLayer(`https://api.mapy.cz/v1/maptiles/outdoor/256/{z}/{x}/{y}?apikey=${API_KEY}`, {
        minZoom: 0,
        maxZoom: 19,
        attribution: '<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
    }).addTo(map);
    const LogoControl = L.Control.extend({
        options: {
            position: 'bottomleft',
        },

        onAdd: function (map) {
            const container = L.DomUtil.create('div');
            const link = L.DomUtil.create('a', '', container);

            link.setAttribute('href', 'http://mapy.cz/');
            link.setAttribute('target', '_blank');
            link.innerHTML = '<img src="https://api.mapy.cz/img/api/logo.svg" />';
            L.DomEvent.disableClickPropagation(link);

            return container;
        },
    });
    new LogoControl().addTo(map);
    // L.control.scale({
    //   position: 'bottomright', 
    //   imperial: false, 
    //   maxWidth: 200,
    //   updateWhenIdle:true}).addTo(map);
    return map
}

function make_marker(dum) {
    var icon = L.icon({
        iconUrl: 'images/jude3.png',
        iconSize: [30, 59], // size of the icon
        iconAnchor: [15, 59], // point of the icon which will correspond to marker's location
    });
    var adresa = dum.ulice + " " + dum.cislo_domu;
    var marker = L.marker([dum.gps_y, dum.gps_x], { icon: icon, markerId: dum.id }).bindTooltip(adresa);
    marker.on('click', function (event) {
        L.DomEvent.stopPropagation(event);
        // var markerId = this.options.markerId;
        console.log(dum.id);
        // console.log(markerId);
        tabulka_request(dum.id, "people")
    });
    return marker
}

function make_markers(domy, mapa) {
    // var markers = L.markerClusterGroup({
    //   keepSpiderfy: true
    // });
    if (domy == false) {
        return false;
    }
    for (var dum of domy) {
        // markers.addLayer(make_marker(dum))
        make_marker(dum).addTo(mapa)
        console.log(dum);
    }
    // mapa.addLayer(markers);
}

function handleTouchStart(event) {
    event.preventDefault();
    centerX = compass.offsetWidth / 2;
    centerY = compass.offsetHeight / 2;
    isMousePressed = true;
    const touchX = event.clientX - compass.getBoundingClientRect().left - centerX;
    const touchY = event.clientY - compass.getBoundingClientRect().top - centerY;

    const distance = Math.sqrt(touchX * touchX + touchY * touchY);
    const maxDistance = compass.offsetWidth / 2;
    const ratio = Math.min(maxDistance / distance, 1);

    executeFunction(touchX * ratio, touchY * ratio);
}

function handleMouseMove(event) {
    if (event.buttons !== 1) return;
    event.preventDefault();
    const touchX = event.clientX - compass.getBoundingClientRect().left - centerX;
    const touchY = event.clientY - compass.getBoundingClientRect().top - centerY;

    const distance = Math.sqrt(touchX * touchX + touchY * touchY);
    const maxDistance = compass.offsetWidth / 2;
    const ratio = Math.min(maxDistance / distance, 1);

    executeFunction(touchX * ratio, touchY * ratio);
}

function handleMouseUp(event) {
    event.preventDefault();
    isMousePressed = false;
    clearTimeout(timeoutId);
}

function executeFunction(x, y) {
    if (!isMousePressed) return;
    console.log('Function executed with coordinates:', x, y);
    mapa.panBy([x * 5, y * 5], { animate: true });
    timeoutId = setTimeout(executeFunction, 100, x, y);
}

async function geokoduj(adresa, mapa) {
    try {
        const url = new URL(`https://api.mapy.cz/v1/geocode`);

        url.searchParams.set('lang', 'cs');
        url.searchParams.set('apikey', API_KEY);
        url.searchParams.set('query', adresa);
        url.searchParams.set('limit', '1');
        [
            'regional.municipality',
            'regional.municipality_part',
            'regional.street',
            'regional.address'
        ].forEach(type => url.searchParams.append('type', type));

        const response = await fetch(url.toString(), {
            mode: 'cors',
        });
        const json = await response.json();

        console.log('geocode', json);
        godpoved = odpoved(json)
        find_user(godpoved.gps_x, godpoved.gps_y, mapa)
    } catch (ex) {
        console.log(ex);
    }
}

async function geokoduj_clik(gps_x, gps_y, mapa) {
    console.log("klik", gps_x, gps_y)
    try {
        const url = new URL(`https://api.mapy.cz/v1/rgeocode/`);

        url.searchParams.set('lang', 'cs');
        url.searchParams.set('lat', gps_x);
        url.searchParams.set('lon', gps_y);
        url.searchParams.set('apikey', API_KEY);
        // url.searchParams.set('limit', '1');
        [
            'regional.municipality',
            'regional.municipality_part',
            'regional.street',
            'regional.address'
        ].forEach(type => url.searchParams.append('type', type));

        console.log(url.toString());
        const response = await fetch(url.toString(), {
            mode: 'cors',
        });
        const json = await response.json();

        console.log('geocode', json);
        godpoved = odpoved(json)
        find_user(godpoved.gps_x, godpoved.gps_y, mapa)
    } catch (ex) {
        console.log(ex);
    }
}

function odpoved(geocoder) { /* Odpověď */
    if (!geocoder.items.length) {
        alert("Tohle místo neznáme.");
        loading_off();
        return;
    }
    var vysledky = geocoder.items[0];
    let gps_x = vysledky.position.lon;
    let gps_y = vysledky.position.lat;

    var mesto = vysledky.regionalStructure.find(item => item.type === "regional.municipality").name;

    var cislo = vysledky.regionalStructure.find(item => item.type === "regional.address");
    if (cislo) {
        cislo = cislo.name;
    }
    else {
        document.getElementById("search").value = mesto;
        // document.getElementById("search_route").value = mesto;
        return { gps_x: gps_x, gps_y: gps_y }
    }
    var lomítka = cislo.split('/');
    // console.log(items);
    let ulice = vysledky.regionalStructure.find(item => item.type === "regional.street")
    if (ulice) {
        ulice = ulice.name;
    }
    else {
        // ulice = mesto;
    }

    var addressInfo = {
        ulice: ulice,
        pscislo: lomítka[0],
        ocislo: lomítka[1],
        mesto: mesto,
        stat: vysledky.regionalStructure.find(item => item.type === "regional.country").name,
        gps_x: vysledky.position.lon,
        gps_y: vysledky.position.lat
    };
    console.log(addressInfo)
    document.getElementById("search").value = mesto + " " + addressInfo.ulice //+ " " + addressInfo.ocislo;
    // document.getElementById("search_route").value = mesto +" "+ addressInfo.ulice //+ " " + addressInfo.ocislo;
    return { gps_x: addressInfo.gps_x, gps_y: addressInfo.gps_y }
    // var value = document.getElementById("")
    // var mestoInput = document.getElementById("nmesto");
    // var uliceInput = document.getElementById("nulice");
    // var cisloDomuInput = document.getElementById("ncislo_domu");
    // var gpsXInput = document.getElementById("gps_x");
    // var gpsYInput = document.getElementById("gps_y");

    // mestoInput.value = addressInfo.mesto;
    // uliceInput.value = addressInfo.ulice;
    // cisloDomuInput.value = addressInfo.ocislo;
    // gpsXInput.value = addressInfo.gps_x;
    // gpsYInput.value = addressInfo.gps_y;

}

function loading(id) {
    let loader = document.getElementById(id);
    loader.innerHTML = `<div class="loader" id="loader">
        <svg class="car" width="102" height="40" xmlns="http://www.w3.org/2000/svg">
            <g transform="translate(2 1)" stroke="#002742" fill="none" fill-rule="evenodd" stroke-linecap="round"
                stroke-linejoin="round">
                <path class="car__body"
                    d="M47.293 2.375C52.927.792 54.017.805 54.017.805c2.613-.445 6.838-.337 9.42.237l8.381 1.863c2.59.576 6.164 2.606 7.98 4.531l6.348 6.732 6.245 1.877c3.098.508 5.609 3.431 5.609 6.507v4.206c0 .29-2.536 4.189-5.687 4.189H36.808c-2.655 0-4.34-2.1-3.688-4.67 0 0 3.71-19.944 14.173-23.902zM36.5 15.5h54.01"
                    stroke-width="3" />
                <ellipse class="car__wheel--left" stroke-width="3.2" fill="#FFF" cx="83.493" cy="30.25" rx="6.922"
                    ry="6.808" />
                <ellipse class="car__wheel--right" stroke-width="3.2" fill="#FFF" cx="46.511" cy="30.25" rx="6.922"
                    ry="6.808" />
                <path class="car__line car__line--top" d="M22.5 16.5H2.475" stroke-width="3" />
                <path class="car__line car__line--middle" d="M20.5 23.5H.4755" stroke-width="3" />
                <path class="car__line car__line--bottom" d="M25.5 9.5h-19" stroke-width="3" />
            </g>
        </svg>
    </div>`;
}
function loading_off() {
    let loader = document.getElementById("loader");
    if (loader) {
        loader.parentElement.removeChild(loader);
    }
    loader = document.getElementById("loader");
    if (loader) {
        loader.parentElement.removeChild(loader);
    }
}

function find_user(x, y, mapa) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            write_users_result(xhr.responseText, mapa);
        }
    };
    let data = new FormData();
    data.append('x', x);
    data.append("y", y);
    xhr.open("POST", "find_users.php", true,);
    xhr.send(data);
}

function write_users_result(response, mapa) {
    // if (!users) {
    //     alert("nikdo tu zatím není")
    //     console.log("Nenalezeni žádní uživatelé");
    //     // loading_off();
    //     return;
    // }
    var users = JSON.parse(response);
    users.forEach(user => {
        console.log(user);
    });
    let response_div = document.getElementById("response");
    response_div.innerHTML = '';

    users.forEach((user) => {
        let user_div = document.createElement("div");
        user_div.classList.add("user");

        user_div.innerHTML = `<div class="name">${user.Name}</div>`;
        const date = new Date(user.Time.replace(' ', 'T'));
        console.log(date);
        const formattedDate = date.toLocaleDateString('cs-CZ', {
            day: '2-digit',
            month: '2-digit'
        });
        const formattedTime = date.toLocaleTimeString('cs-CZ', {
            hour: '2-digit',
            minute: '2-digit'
        });

        user_div.innerHTML += `<div class="time">
                   Vyjíždí ${formattedDate} ${formattedTime}
                </div>`;
        // user_div.innerHTML += `<div class=min_distance>
        //             Minimální vzdálenost: ${user.MinDistance} km
        //         </div>`;
        user_div.innerHTML += `<div class="route">
                        <input type="button" id="ID_${user.ID}" value="Zobrazit cestu" user_id=${user.ID}>
                    </div>`;

        response_div.appendChild(user_div);
        document.getElementById(`ID_${user.ID}`).addEventListener("click", function () {
            // console.log("use_id", user.ID);
            // console.log(document.getElementById(`ID_${user.ID}`))
            // console.log(`ID_${user.ID}`)
            removeRoute();
            show_route(user, mapa);
        });

    });
}

function show_route(user, mapa) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let route = xhr.responseText;
            route = JSON.parse(route);
            let geo = route["geometry"];
            // geo = JSON.stringify(geo);
            console.log(geo);
            var routeLayer = L.geoJSON(geo, {
                style: function (feature) {
                    return {
                        color: "#0001ff",
                        weight: 5,
                        opacity: 0.65
                    };
                }
            });
            routeLayer.addTo(mapa);
            mapa.fitBounds(routeLayer.getBounds());
        }
    };
    let data = new FormData();
    data.append('user_id', user.ID);
    xhr.open("POST", "get_route.php", true);
    xhr.send(data);
}

async function send_offer(name, time, route, dalnice, prujezd) {
    try {
        let city = await found_city(route);
        let throughs = await Promise.all(prujezd.map(p => found_city(p)));
        console.log("city", city);
        console.log("prujezd", prujezd);
        const new_route = await make_route(city.gps_x, city.gps_y, dalnice, throughs);

        let geo = new_route["geometry"];
        var routeLayer = L.geoJSON(geo, {
            style: function (feature) {
                return {
                    color: "#0001ff",
                    weight: 5,
                    opacity: 0.65
                };
            }
        });
        routeLayer.addTo(mapa);
        mapa.fitBounds(routeLayer.getBounds());


        let gps_x = city.gps_x;
        let gps_y = city.gps_y;
        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
                loading_off();
            }
        };
        let data = new FormData();
        data.append('name', name);
        data.append('time', time);
        data.append('route', JSON.stringify(new_route));
        data.append('gps_x', gps_x);
        data.append('gps_y', gps_y);
        xhr.open("POST", "send_offer.php", true);
        xhr.send(data);

        let formObject = {};
        data.forEach((value, key) => {
            formObject[key] = value;
        });
        let json = JSON.stringify(formObject);
        console.log(json);

        console.log("send_offer", JSON.stringify(new_route));
    } catch (ex) {
        console.log(ex);
    }
}

async function found_city(adresa) {
    try {
        const url = new URL(`https://api.mapy.cz/v1/geocode`);

        url.searchParams.set('lang', 'cs');
        url.searchParams.set('apikey', API_KEY);
        url.searchParams.set('query', adresa);
        url.searchParams.set('limit', '1');
        [
            'regional.municipality',
            'regional.municipality_part',
            'regional.street',
            'regional.address'
        ].forEach(type => url.searchParams.append('type', type));

        const response = await fetch(url.toString(), {
            mode: 'cors',
        });
        const json = await response.json();

        console.log('geocode', json);
        godpoved = odpoved(json)
        return { gps_x: godpoved.gps_x, gps_y: godpoved.gps_y }
    } catch (ex) {
        console.log(ex);
    }
}
async function make_route(gps_x, gps_y, dalnice, prujezd) {
    try {
        const url = new URL(`https://api.mapy.cz/v1/routing/route`);

        console.log("make_route", gps_x, gps_y, dalnice, prujezd);
        url.searchParams.set('lang', 'cs');
        url.searchParams.set('apikey', API_KEY);
        url.searchParams.set('start', gps_x + "," + gps_y);
        url.searchParams.set('end', 17.2790949 + "," + 49.5963855);
        if (prujezd) {
            prujezd.forEach(p => {
                url.searchParams.append('waypoints', p.gps_x + "," + p.gps_y);
            });
        }
        url.searchParams.set('routeType', 'car_fast');
        url.searchParams.set('avoidToll', dalnice);

        console.log(url.toString());

        const response = await fetch(url.toString(), {
            mode: 'cors',
        });
        const json = await response.json();

        console.log('geocode', json);

        return json;
    } catch (ex) {
        console.log(ex);
    }
}

function removeRoute() {
    mapa.eachLayer(function (layer) {
        if (layer instanceof L.GeoJSON) {
            mapa.removeLayer(layer);
        }
    });
}