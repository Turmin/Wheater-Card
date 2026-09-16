<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weer</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            font-family: Arial, Helvetica, sans-serif;
        }

        .weather-card {
            width: 100%;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 14px;
            background: #ffffff;
            color: #151515;
            overflow: hidden;
        }

        .weather-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .label {
            margin-bottom: 5px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #e30613;
        }

        .weather-title {
            max-width: 100%;
            margin: 0;
            font-size: 22px;
            line-height: 1.15;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .weather-time {
            flex: 0 0 auto;
            margin: 13px 0 0;
            font-size: 22px;
            line-height: 1;
            font-weight: 800;
            color: #333333;
        }

        .weather-status {
            min-height: 16px;
            margin: -5px 0 0;
            font-size: 12px;
            color: #777777;
        }

        .weather-content {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .weather-panel {
            min-width: 0;
            padding: 11px;
            border-radius: 12px;
            background: #f4f4f4;
        }

        .weather-panel h2,
        .chart-header h2 {
            margin: 0;
            font-size: 13px;
            line-height: 1.2;
            font-weight: 800;
        }

        .weather-panel h2 {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 6px;
        }

        .forecast-time {
            font-size: 11px;
            font-weight: 700;
            color: #777777;
        }

        .weather-stats {
            display: grid;
            gap: 7px;
            margin: 11px 0 0;
        }

        .weather-stat {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }

        .weather-stat dt {
            min-width: 0;
            font-size: 11px;
            line-height: 1.2;
            color: #777777;
        }

        .weather-stat dd {
            flex: 0 0 auto;
            margin: 0;
            font-size: 13px;
            line-height: 1.2;
            font-weight: 800;
            white-space: nowrap;
        }

        .temperature-chart {
            min-width: 0;
            padding: 11px;
            border-radius: 12px;
            background: #fafafa;
            border: 1px solid #eeeeee;
        }

        .chart-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }

        .chart-range {
            font-size: 11px;
            color: #777777;
        }

        .chart-wrap {
            width: 100%;
            height: 128px;
            margin-top: 6px;
        }

        .temperature-chart svg {
            display: block;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .chart-grid line {
            stroke: #e7e7e7;
            stroke-width: 1;
        }

        .chart-grid text,
        .chart-labels text {
            fill: #888888;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }

        .chart-line {
            fill: none;
            stroke: #b64b52;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 3;
        }

        .chart-area {
            fill: url(#temperature-area);
            stroke: none;
        }

        .chart-point {
            fill: #ffffff;
            stroke: #b64b52;
            stroke-width: 3;
        }

        .chart-value {
            fill: #333333;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            font-weight: 700;
            text-anchor: middle;
        }

        .weather-attribution,
        .weather-error {
            margin: 0;
            font-size: 10px;
            line-height: 1.3;
        }

        .weather-attribution {
            color: #999999;
        }

        .weather-attribution a {
            color: inherit;
        }

        .weather-error {
            padding: 9px 10px;
            border-radius: 10px;
            background: #fff1f1;
            color: #a51d27;
        }

        [hidden] {
            display: none !important;
        }

        @media (max-width: 360px) {
            .weather-card {
                gap: 10px;
                padding: 12px;
            }

            .weather-title,
            .weather-time {
                font-size: 18px;
            }

            .weather-time {
                margin-top: 11px;
            }

            .weather-content {
                grid-template-columns: 1fr;
            }

            .chart-wrap {
                height: 112px;
            }
        }
    </style>
</head>
<body>

<article class="weather-card" aria-live="polite">
    <header class="weather-header">
        <div>
            <div class="label">Weer</div>
            <h1 class="weather-title" id="location-name">Locatie bepalen…</h1>
        </div>
        <time class="weather-time" id="current-time" datetime="">--:--</time>
    </header>

    <p class="weather-status" id="weather-status">Locatie wordt bepaald…</p>

    <div class="weather-content">
        <section class="weather-panel current-weather" aria-labelledby="current-weather-title">
            <h2 id="current-weather-title">Nu</h2>
            <dl class="weather-stats">
                <div class="weather-stat">
                    <dt>Temperatuur</dt>
                    <dd id="current-temp">-- °C</dd>
                </div>
                <div class="weather-stat">
                    <dt>Luchtvochtigheid</dt>
                    <dd id="current-humidity">-- %</dd>
                </div>
                <div class="weather-stat">
                    <dt>Regen</dt>
                    <dd id="current-rain">-- mm</dd>
                </div>
                <div class="weather-stat">
                    <dt>Neerslagkans</dt>
                    <dd id="current-precipitation-chance">-- %</dd>
                </div>
                <div class="weather-stat">
                    <dt>Windkracht</dt>
                    <dd id="current-wind-speed">-- km/h</dd>
                </div>
            </dl>
        </section>

        <section class="weather-panel forecast-weather" aria-labelledby="forecast-weather-title">
            <h2 id="forecast-weather-title">
                Komend uur
                <span class="forecast-time" id="forecast-time">--:--</span>
            </h2>
            <dl class="weather-stats">
                <div class="weather-stat">
                    <dt>Temperatuur</dt>
                    <dd id="forecast-temp">-- °C</dd>
                </div>
                <div class="weather-stat">
                    <dt>Luchtvochtigheid</dt>
                    <dd id="forecast-humidity">-- %</dd>
                </div>
                <div class="weather-stat">
                    <dt>Regen</dt>
                    <dd id="forecast-rain">-- mm</dd>
                </div>
                <div class="weather-stat">
                    <dt>Neerslagkans</dt>
                    <dd id="forecast-precipitation-chance">-- %</dd>
                </div>
                <div class="weather-stat">
                    <dt>Windkracht</dt>
                    <dd id="forecast-wind-speed">-- km/h</dd>
                </div>
            </dl>
        </section>
    </div>

    <section class="temperature-chart" id="temperature-chart" aria-labelledby="temperature-chart-title" hidden>
        <div class="chart-header">
            <h2 id="temperature-chart-title">Temperatuur</h2>
            <span class="chart-range">Nu + komend uur</span>
        </div>
        <div class="chart-wrap">
            <svg id="temperature-chart-svg" viewBox="0 0 520 160" role="img" aria-labelledby="temperature-chart-title">
                <title id="temperature-chart-description">Temperatuur voor nu en het komende uur</title>
            </svg>
        </div>
    </section>

    <p class="weather-error" id="weather-error" hidden></p>
    <p class="weather-attribution">Gegevens van <a href="https://open-meteo.com/" target="_blank" rel="noopener noreferrer">Open-Meteo</a></p>
</article>

<script>
    (function () {
        'use strict';

        var GEOLOCATION_TIMEOUT = 10000;
        var REFRESH_INTERVAL = 10 * 60 * 1000;
        var locationName = document.getElementById('location-name');
        var currentTime = document.getElementById('current-time');
        var weatherStatus = document.getElementById('weather-status');
        var weatherError = document.getElementById('weather-error');
        var temperatureChart = document.getElementById('temperature-chart');
        var temperatureChartSvg = document.getElementById('temperature-chart-svg');
        var activeLocation = null;
        var activeTimezone = null;
        var refreshTimer = null;

        function isFiniteNumber(value) {
            return typeof value === 'number' && isFinite(value);
        }

        function readNumber(value) {
            var number = typeof value === 'number' ? value : Number(value);

            return isFiniteNumber(number) ? number : null;
        }

        function arrayValue(values, index) {
            if (!Array.isArray(values) || index < 0 || index >= values.length) {
                return null;
            }

            return readNumber(values[index]);
        }

        function formatNumber(value, decimals) {
            var number = readNumber(value);

            if (number === null) {
                return '--';
            }

            return number.toLocaleString('nl-NL', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }

        function setMetric(id, value, unit, decimals) {
            var element = document.getElementById(id);
            var number = readNumber(value);

            if (!element) {
                return;
            }

            element.textContent = number === null ? '-- ' + unit : formatNumber(number, decimals) + ' ' + unit;
        }

        function formatClockValue(value) {
            if (typeof value !== 'string') {
                return '--:--';
            }

            var timePart = value.split('T')[1] || '';

            return timePart.slice(0, 5) || '--:--';
        }

        function updateClock() {
            var now = new Date();

            if (activeTimezone && typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
                try {
                    currentTime.textContent = new Intl.DateTimeFormat('nl-NL', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                        timeZone: activeTimezone
                    }).format(now);
                    return;
                } catch (error) {
                    // Fall back to the API time when the timezone is unavailable locally.
                }
            }

            if (!currentTime.textContent || currentTime.textContent === '--:--') {
                currentTime.textContent = formatClockValue(currentTime.getAttribute('datetime'));
            }
        }

        function setCurrentTime(value) {
            var clockValue = formatClockValue(value);

            currentTime.setAttribute('datetime', typeof value === 'string' ? value : '');
            currentTime.textContent = clockValue;
            updateClock();
        }

        function fetchJson(url) {
            return fetch(url, {
                headers: {
                    Accept: 'application/json'
                }
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('De weer-API gaf foutcode ' + response.status + '.');
                }

                return response.json();
            });
        }

        function parseCoordinate(value, minimum, maximum) {
            if (typeof value !== 'string' || value.trim() === '') {
                return null;
            }

            var number = Number(value);

            if (!isFiniteNumber(number) || number < minimum || number > maximum) {
                return null;
            }

            return number;
        }

        function parseCoordinatePair(value) {
            if (typeof value !== 'string') {
                return null;
            }

            var parts = value.split(',');

            if (parts.length !== 2) {
                return null;
            }

            var latitude = parseCoordinate(parts[0], -90, 90);
            var longitude = parseCoordinate(parts[1], -180, 180);

            if (latitude === null || longitude === null) {
                return null;
            }

            return {
                latitude: latitude,
                longitude: longitude,
                name: 'Opgegeven locatie'
            };
        }

        function getUrlLocation() {
            var params = new URLSearchParams(window.location.search);
            var locationQuery = (params.get('location') || '').trim();
            var latitude = parseCoordinate(params.get('lat') || params.get('latitude'), -90, 90);
            var longitude = parseCoordinate(params.get('lon') || params.get('lng') || params.get('longitude'), -180, 180);

            if (locationQuery.length > 100) {
                throw new Error('De locatieparameter is te lang.');
            }

            if (locationQuery) {
                return parseCoordinatePair(locationQuery) || {
                    query: locationQuery
                };
            }

            if (latitude !== null && longitude !== null) {
                return {
                    latitude: latitude,
                    longitude: longitude,
                    name: 'Opgegeven locatie'
                };
            }

            if (params.has('lat') || params.has('latitude') || params.has('lon') || params.has('lng') || params.has('longitude')) {
                throw new Error('Gebruik geldige coördinaten, bijvoorbeeld ?lat=52.37&lon=4.90.');
            }

            return null;
        }

        function geocodeLocation(query) {
            var url = new URL('https://geocoding-api.open-meteo.com/v1/search');

            url.searchParams.set('name', query);
            url.searchParams.set('count', '1');
            url.searchParams.set('language', 'nl');
            url.searchParams.set('format', 'json');

            return fetchJson(url.toString()).then(function (data) {
                var result = data && Array.isArray(data.results) ? data.results[0] : null;

                if (!result || !isFiniteNumber(Number(result.latitude)) || !isFiniteNumber(Number(result.longitude))) {
                    throw new Error('Locatie niet gevonden: ' + query + '.');
                }

                var parts = [result.name, result.admin1, result.country];
                var uniqueParts = [];

                parts.forEach(function (part) {
                    if (part && uniqueParts.indexOf(part) === -1) {
                        uniqueParts.push(part);
                    }
                });

                return {
                    latitude: Number(result.latitude),
                    longitude: Number(result.longitude),
                    name: uniqueParts.join(', ') || query
                };
            });
        }

        function getBrowserLocation() {
            return new Promise(function (resolve, reject) {
                if (!navigator.geolocation) {
                    reject(new Error('Deze browser ondersteunt geen automatische locatiebepaling. Gebruik ?location=Amsterdam.'));
                    return;
                }

                navigator.geolocation.getCurrentPosition(function (position) {
                    var latitude = readNumber(position.coords.latitude);
                    var longitude = readNumber(position.coords.longitude);

                    if (latitude === null || longitude === null) {
                        reject(new Error('De browser gaf geen geldige coördinaten terug.'));
                        return;
                    }

                    resolve({
                        latitude: latitude,
                        longitude: longitude,
                        name: 'Huidige locatie'
                    });
                }, function (error) {
                    var message = 'Automatische locatiebepaling mislukt';

                    if (error && error.code === error.PERMISSION_DENIED) {
                        message = 'Toegang tot de locatie is geweigerd';
                    } else if (error && error.code === error.TIMEOUT) {
                        message = 'De locatie kon niet op tijd worden bepaald';
                    }

                    reject(new Error(message + '. Gebruik ?location=Amsterdam.'));
                }, {
                    enableHighAccuracy: false,
                    maximumAge: 15 * 60 * 1000,
                    timeout: GEOLOCATION_TIMEOUT
                });
            });
        }

        function resolveLocation() {
            var urlLocation = getUrlLocation();

            if (!urlLocation) {
                return getBrowserLocation();
            }

            if (urlLocation.query) {
                return geocodeLocation(urlLocation.query);
            }

            return Promise.resolve(urlLocation);
        }

        function buildForecastUrl(location) {
            var url = new URL('https://api.open-meteo.com/v1/forecast');

            url.searchParams.set('latitude', location.latitude.toFixed(4));
            url.searchParams.set('longitude', location.longitude.toFixed(4));
            url.searchParams.set('current', [
                'temperature_2m',
                'relative_humidity_2m',
                'rain',
                'wind_speed_10m'
            ].join(','));
            url.searchParams.set('hourly', [
                'temperature_2m',
                'relative_humidity_2m',
                'rain',
                'precipitation_probability',
                'wind_speed_10m'
            ].join(','));
            url.searchParams.set('forecast_hours', '3');
            url.searchParams.set('timezone', 'auto');
            url.searchParams.set('temperature_unit', 'celsius');
            url.searchParams.set('wind_speed_unit', 'kmh');
            url.searchParams.set('precipitation_unit', 'mm');

            return url.toString();
        }

        function getForecastIndexes(hourly, current) {
            var times = Array.isArray(hourly.time) ? hourly.time : [];
            var currentApiTime = typeof current.time === 'string' ? current.time : '';
            var nextIndex = -1;

            for (var index = 0; index < times.length; index += 1) {
                if (currentApiTime && times[index] > currentApiTime) {
                    nextIndex = index;
                    break;
                }
            }

            var currentIndex = nextIndex > 0 ? nextIndex - 1 : 0;
            var forecastIndex = nextIndex >= 0 ? nextIndex : currentIndex;

            return {
                currentIndex: currentIndex,
                forecastIndex: forecastIndex,
                times: times
            };
        }

        function setForecastMetric(prefix, values, index) {
            setMetric(prefix + '-temp', arrayValue(values.temperature_2m, index), '°C', 1);
            setMetric(prefix + '-humidity', arrayValue(values.relative_humidity_2m, index), '%', 0);
            setMetric(prefix + '-rain', arrayValue(values.rain, index), 'mm', 1);
            setMetric(prefix + '-precipitation-chance', arrayValue(values.precipitation_probability, index), '%', 0);
            setMetric(prefix + '-wind-speed', arrayValue(values.wind_speed_10m, index), 'km/h', 0);
        }

        function escapeSvg(value) {
            return String(value).replace(/[&<>"']/g, function (character) {
                var entities = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&apos;'
                };

                return entities[character];
            });
        }

        function renderTemperatureChart(points) {
            if (!Array.isArray(points) || points.length < 2) {
                temperatureChart.hidden = true;
                return;
            }

            var left = 42;
            var right = 508;
            var top = 23;
            var bottom = 128;
            var temperatures = points.map(function (point) {
                return point.temperature;
            });
            var minimum = Math.min.apply(Math, temperatures);
            var maximum = Math.max.apply(Math, temperatures);

            if (minimum === maximum) {
                minimum -= 1;
                maximum += 1;
            } else {
                var padding = Math.max(0.5, (maximum - minimum) * 0.2);
                minimum -= padding;
                maximum += padding;
            }

            var range = maximum - minimum;
            var chartPoints = points.map(function (point, index) {
                var x = points.length === 1 ? (left + right) / 2 : left + ((right - left) * index / (points.length - 1));
                var y = bottom - ((point.temperature - minimum) / range * (bottom - top));

                return {
                    x: x,
                    y: y,
                    temperature: point.temperature,
                    time: point.time
                };
            });
            var linePoints = chartPoints.map(function (point) {
                return point.x.toFixed(1) + ',' + point.y.toFixed(1);
            }).join(' ');
            var areaPoints = left + ',' + bottom + ' ' + linePoints + ' ' + right + ',' + bottom;
            var gridValues = [maximum, (minimum + maximum) / 2, minimum];
            var grid = gridValues.map(function (value, index) {
                var y = top + ((bottom - top) * index / (gridValues.length - 1));

                return '<line x1="' + left + '" x2="' + right + '" y1="' + y.toFixed(1) + '" y2="' + y.toFixed(1) + '"></line>' +
                    '<text x="4" y="' + (y + 4).toFixed(1) + '">' + escapeSvg(formatNumber(value, 1) + '°') + '</text>';
            }).join('');
            var labels = chartPoints.map(function (point) {
                return '<text x="' + point.x.toFixed(1) + '" y="151" text-anchor="middle">' + escapeSvg(point.time) + '</text>';
            }).join('');
            var values = chartPoints.map(function (point) {
                return '<text class="chart-value" x="' + point.x.toFixed(1) + '" y="' + Math.max(14, point.y - 9).toFixed(1) + '">' +
                    escapeSvg(formatNumber(point.temperature, 1) + '°') + '</text>' +
                    '<circle class="chart-point" cx="' + point.x.toFixed(1) + '" cy="' + point.y.toFixed(1) + '" r="5"></circle>';
            }).join('');

            temperatureChartSvg.innerHTML = '<defs>' +
                '<linearGradient id="temperature-area" x1="0" x2="0" y1="0" y2="1">' +
                    '<stop offset="0%" stop-color="#b64b52" stop-opacity="0.22"></stop>' +
                    '<stop offset="100%" stop-color="#b64b52" stop-opacity="0.02"></stop>' +
                '</linearGradient>' +
            '</defs>' +
            '<g class="chart-grid" aria-hidden="true">' + grid + '</g>' +
            '<polygon class="chart-area" points="' + areaPoints + '"></polygon>' +
            '<polyline class="chart-line" points="' + linePoints + '"></polyline>' +
            '<g class="chart-labels" aria-hidden="true">' + labels + '</g>' +
            '<g aria-hidden="true">' + values + '</g>';
            temperatureChartSvg.setAttribute('aria-label', 'Temperatuur: ' + points.map(function (point) {
                return point.time + ' ' + formatNumber(point.temperature, 1) + ' graden';
            }).join(', ') + '.');
            temperatureChart.hidden = false;
        }

        function renderWeather(data, location) {
            var current = data && data.current ? data.current : null;
            var hourly = data && data.hourly ? data.hourly : null;

            if (!current || !hourly) {
                throw new Error('De weer-API stuurde geen bruikbare gegevens terug.');
            }

            var indexes = getForecastIndexes(hourly, current);
            var currentTemperature = readNumber(current.temperature_2m);
            var forecastTemperature = arrayValue(hourly.temperature_2m, indexes.forecastIndex);

            if (currentTemperature === null || forecastTemperature === null) {
                throw new Error('De weer-API stuurde geen temperatuur voor het komende uur.');
            }

            activeTimezone = typeof data.timezone === 'string' ? data.timezone : null;
            locationName.textContent = location.name;
            setCurrentTime(current.time);

            setMetric('current-temp', current.temperature_2m, '°C', 1);
            setMetric('current-humidity', current.relative_humidity_2m, '%', 0);
            setMetric('current-rain', current.rain, 'mm', 1);
            setMetric('current-precipitation-chance', arrayValue(hourly.precipitation_probability, indexes.currentIndex), '%', 0);
            setMetric('current-wind-speed', current.wind_speed_10m, 'km/h', 0);
            setForecastMetric('forecast', hourly, indexes.forecastIndex);

            document.getElementById('forecast-time').textContent = formatClockValue(indexes.times[indexes.forecastIndex]);
            weatherStatus.textContent = 'Bijgewerkt voor ' + location.name + '.';
            weatherError.hidden = true;

            renderTemperatureChart([
                {
                    time: 'Nu',
                    temperature: currentTemperature
                },
                {
                    time: formatClockValue(indexes.times[indexes.forecastIndex]),
                    temperature: forecastTemperature
                }
            ]);
        }

        function showInitialError(error) {
            var message = error && error.message ? error.message : 'De weergegevens konden niet worden geladen.';

            locationName.textContent = 'Geen locatie';
            weatherStatus.textContent = 'Geen weergegevens beschikbaar.';
            weatherError.textContent = message;
            weatherError.hidden = false;
        }

        function loadWeather(location, initialLoad) {
            if (initialLoad) {
                weatherStatus.textContent = 'Weer ophalen…';
            }

            return fetchJson(buildForecastUrl(location)).then(function (data) {
                renderWeather(data, location);
            }).catch(function (error) {
                if (initialLoad) {
                    showInitialError(error);
                } else {
                    weatherStatus.textContent = 'Laatste gegevens worden getoond; vernieuwen mislukt.';
                }
            });
        }

        function start() {
            updateClock();

            try {
                resolveLocation().then(function (location) {
                    activeLocation = location;
                    return loadWeather(location, true);
                }).then(function () {
                    if (refreshTimer) {
                        window.clearInterval(refreshTimer);
                    }

                    refreshTimer = window.setInterval(function () {
                        if (activeLocation) {
                            loadWeather(activeLocation, false);
                        }
                    }, REFRESH_INTERVAL);
                }).catch(showInitialError);
            } catch (error) {
                showInitialError(error);
            }
        }

        updateClock();
        window.setInterval(updateClock, 1000);
        start();
    }());
</script>

</body>
</html>
