# Weather card

Open `weather-card.php` in a browser or embed it as a card. It uses Open-Meteo for location lookup and weather data, and Chart.js 4.5.1 for interactive temperature and rain charts.

## Features

- Responsive layout.
- Skeleton loading state while the location and weather data are fetched.
- Browser geolocation, place-name lookup, and exact coordinate support.
- Interactive temperature and rain forecasts with Chart.js.
- Automatic weather refresh every ten minutes.
- English interface with compact Open-Meteo attribution.

Location selection:

- Without a query parameter, the browser location is requested.
- Use `weather-card.php?location=Amsterdam` for a place name or postal code.
- Use `weather-card.php?lat=52.37&lon=4.90` for exact coordinates.
- Add `darkmode=1` to either URL to use the dark theme, for example `weather-card.php?location=Amsterdam&darkmode=1`.

For transparent rounded corners in an iframe, the embedding iframe must also allow a transparent background, for example with `allowtransparency="true"` and `background: transparent`.

Browser geolocation requires a secure context (HTTPS or localhost). The card refreshes weather data automatically every ten minutes. Hover or tap a chart point to see its value.
