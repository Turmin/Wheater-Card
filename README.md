# Weather card

Open `weather-card.php` in a browser or embed it as a card. It uses Open-Meteo for location lookup and weather data, and Chart.js 4.5.1 for interactive temperature and rain charts.

Location selection:

- Without a query parameter, the browser location is requested.
- Use `weather-card.php?location=Amsterdam` for a place name or postal code.
- Use `weather-card.php?lat=52.37&lon=4.90` for exact coordinates.

Browser geolocation requires a secure context (HTTPS or localhost). The card refreshes weather data automatically every ten minutes. Hover or tap a chart point to see its value.
