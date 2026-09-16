# Weather card

Open `weather-card.php` in a browser or embed it as a card. The card uses Open-Meteo for geocoding and weather data.

Location selection:

- Without a query parameter, the browser location is requested.
- Use `weather-card.php?location=Amsterdam` for a place name or postal code.
- Use `weather-card.php?lat=52.37&lon=4.90` for exact coordinates.

Browser geolocation requires a secure context (HTTPS or localhost). The card refreshes weather data automatically every ten minutes.
