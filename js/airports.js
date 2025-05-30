document.addEventListener('DOMContentLoaded', function () {

    // Ładowanie danych z JSON
    fetch('/airports.json')
        .then(response => response.json())
        .then(data => {
            generatePricingTable(data);
            initMap(data.airports);
            populateAirportSelect(data.airports);
        })
        .catch(error => console.error('Błąd ładowania danych:', error));

    // Funkcja generująca tabelę cen
    function generatePricingTable(data) {
        const tableBody = document.getElementById('pricing-table-body');
        const staticRows = tableBody.querySelectorAll('tr');
        
        // Dodaj lotniska przed istniejącymi wierszami
        data.airports.forEach(airport => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${airport.name} <span class="text-secondary">(${airport.code})</span></td>
                <td>${airport.price_1_3} zł</td>
                <td>${airport.price_3_8} zł</td>
            `;
            tableBody.insertBefore(row, staticRows[0]);
        });
    }

    // Funkcja wypełniająca select w formularzu
    function populateAirportSelect(airports) {
        const select = document.getElementById('airport');
        airports.forEach(airport => {
            const option = document.createElement('option');
            option.value = `${airport.name} (${airport.code})`;
            option.textContent = `${airport.name} (${airport.code})`;
            select.appendChild(option);
        });
    }

});