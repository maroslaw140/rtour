function initMap(airports) {

    const locations = [...airports, {
        name: "Opole",
        address: "Opole",
        lat: 50.6721,
        lon: 17.9253,
        price_1_3: "",
        price_3_8: ""
    }];

    const mapa = L.map('mapka', {
        center: [52.0692, 19.4802],
        zoom: 5.5,
        zoomControl: false,
        dragging: false,
        touchZoom: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        tap: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(mapa);

    const defaultIcon = L.icon({
        iconUrl: "./layout/map.png",
        iconSize: [25, 25],
        iconAnchor: [12, 25],
        popupAnchor: [1, -34]
    });

    locations.forEach(location => {
        const popupContent = location.price_1_3 ? 
            `<b>${location.price_1_3} zł</b><br>${location.address}` : 
            `<b>${location.name}</b><br>${location.address}`;
        
        L.marker([location.lat, location.lon], { icon: defaultIcon })
            .addTo(mapa)
            .bindPopup(popupContent);
    });
}