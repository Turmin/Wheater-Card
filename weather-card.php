<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weather</title>
    <script
        src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"
        integrity="sha384-jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ"
        crossorigin="anonymous"
    ></script>

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
            height: 100%;
            max-width: none;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px;
            background: #ffffff;
            color: #151515;
            overflow: hidden;
        }

        .weather-summary {
            flex: 0 0 112px;
            width: 112px;
            height: 112px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            padding: 10px 6px 9px;
            border-radius: 14px;
            border: 1px solid #f2d27b;
            background: linear-gradient(145deg, #fff8df 0%, #ffe9a8 100%);
            text-align: center;
        }

        .weather-symbol {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 32px;
            font-size: 32px;
            line-height: 1;
        }

        .summary-temperature {
            max-width: 100%;
            font-size: 24px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.04em;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .weather-summary-label {
            margin-top: 1px;
            font-size: 9px;
            line-height: 1;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #777777;
        }

        .weather-info {
            min-width: 0;
            width: 0;
            flex: 1 1 0;
        }

        .weather-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .weather-heading-copy {
            min-width: 0;
            flex: 1;
        }

        .label {
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #e30613;
        }

        .weather-title {
            margin: 0 0 5px;
            font-size: 22px;
            line-height: 1.15;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .location-details {
            max-width: 100%;
            margin: 0;
            font-size: 13px;
            line-height: 1.25;
            font-weight: 600;
            color: #777777;
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
            min-height: 12px;
            margin: 0;
            font-size: 11px;
            line-height: 1.2;
            color: #777777;
        }

        .section-heading,
        .forecast-heading,
        .chart-heading {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }

        .weather-current {
            min-width: 0;
            margin-top: 10px;
        }

        .section-title,
        .forecast-title,
        .chart-title {
            margin: 0;
            font-size: 11px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #e30613;
        }

        .weather-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin: 8px 0 0;
        }

        .weather-stat,
        .forecast-stat {
            min-width: 0;
            padding: 6px 8px;
            border: 1px solid transparent;
            border-radius: 10px;
            background: #f4f4f4;
        }

        .weather-stat:nth-child(1),
        .forecast-stat:nth-child(1) {
            border-color: #cfe5f5;
            background: #eef8ff;
        }

        .weather-stat:nth-child(2),
        .forecast-stat:nth-child(2) {
            border-color: #cdeeea;
            background: #effcf9;
        }

        .weather-stat:nth-child(3),
        .forecast-stat:nth-child(3) {
            border-color: #f2dfae;
            background: #fff9e8;
        }

        .weather-stat:nth-child(4),
        .forecast-stat:nth-child(4) {
            border-color: #ded5f2;
            background: #f7f3ff;
        }

        .weather-stat:nth-child(5),
        .forecast-stat:nth-child(5) {
            border-color: #d5e8d1;
            background: #f3fbf1;
        }

        .weather-stat dt,
        .forecast-stat dt {
            margin: 0;
            font-size: 10px;
            line-height: 1.1;
            color: #777777;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .weather-stat dd,
        .forecast-stat dd {
            margin: 3px 0 0;
            font-size: 13px;
            line-height: 1.1;
            font-weight: 800;
            white-space: nowrap;
        }

        .weather-next {
            min-width: 0;
            margin-top: 10px;
        }

        .forecast-time {
            font-size: 11px;
            line-height: 1.2;
            font-weight: 700;
            color: #777777;
        }

        .forecast-stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
            margin: 7px 0 0;
        }

        .forecast-stat {
            display: inline-flex;
            align-items: baseline;
            gap: 4px;
            padding: 5px 8px;
        }

        .forecast-stat dd {
            margin: 0;
            font-size: 12px;
        }

        .charts-grid {
            width: 100%;
            min-width: 0;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .chart-card {
            min-width: 0;
            width: 100%;
            overflow: hidden;
            padding: 8px 10px;
            border: 1px solid #eeeeee;
            border-top: 3px solid #b64b52;
            border-radius: 12px;
            background: #fffafb;
        }

        .chart-card:nth-child(2) {
            border-top-color: #4a749a;
            background: #f8fbff;
        }

        .chart-range {
            flex: 0 0 auto;
            font-size: 10px;
            color: #777777;
            white-space: nowrap;
        }

        .chart-title {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chart-wrap {
            position: relative;
            width: 100%;
            min-width: 0;
            height: 86px;
            margin-top: 4px;
        }

        .weather-chart-canvas {
            display: block;
            max-width: 100%;
            min-width: 0;
            width: 100% !important;
            height: 100% !important;
        }

        .weather-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 8px;
        }

        .weather-footer .weather-status {
            flex: 1 1 auto;
        }

        .chart-fallback,
        .weather-error {
            margin: 8px 0 0;
            padding: 8px 10px;
            border-radius: 10px;
            font-size: 11px;
            line-height: 1.3;
        }

        .chart-fallback {
            background: #f4f4f4;
            color: #777777;
        }

        .weather-error {
            background: #fff1f1;
            color: #a51d27;
        }

        .weather-attribution {
            margin: 0;
            white-space: nowrap;
            font-size: 10px;
            line-height: 1.2;
            color: #999999;
        }

        .weather-attribution a {
            color: inherit;
        }

        .skeleton {
            position: relative;
            display: inline-block;
            overflow: hidden;
            vertical-align: middle;
            border-radius: 6px;
            background: #e9e9e9;
            color: transparent;
        }

        .skeleton::after {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.65), transparent);
            content: "";
            animation: skeleton-shimmer 1.4s ease-in-out infinite;
            transform: translateX(-100%);
        }

        .skeleton-symbol {
            width: 34px;
            height: 34px;
            border-radius: 50%;
        }

        .skeleton-summary-temperature {
            width: 67px;
            height: 27px;
        }

        .skeleton-title {
            width: 170px;
            height: 25px;
        }

        .skeleton-details {
            width: 145px;
            height: 15px;
        }

        .skeleton-time {
            width: 51px;
            height: 23px;
        }

        .skeleton-status {
            width: 95px;
            height: 12px;
        }

        .skeleton-value {
            width: 45px;
            height: 14px;
        }

        .skeleton-forecast-time {
            width: 38px;
            height: 12px;
        }

        .skeleton-chart {
            width: 100%;
            height: 100%;
            border-radius: 6px;
        }

        [hidden] {
            display: none !important;
        }

        @keyframes skeleton-shimmer {
            to {
                transform: translateX(100%);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .skeleton::after {
                animation: none;
            }
        }

        @media (max-width: 620px) {
            .weather-card {
                gap: 12px;
                padding: 12px;
            }

            .weather-summary {
                flex-basis: 86px;
                width: 86px;
                height: 86px;
                gap: 2px;
                padding: 8px 4px 7px;
                border-radius: 12px;
            }

            .summary-temperature {
                font-size: 20px;
            }

            .weather-symbol {
                width: 30px;
                height: 28px;
                font-size: 28px;
            }

            .weather-title,
            .weather-time {
                font-size: 18px;
            }

            .weather-time {
                margin-top: 11px;
            }

            .weather-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .forecast-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .weather-footer {
                align-items: flex-start;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 420px) {
            .weather-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .forecast-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .weather-attribution {
                white-space: normal;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 360px) {
            .weather-card {
                gap: 12px;
                padding: 12px;
                border-radius: 14px;
            }

            .weather-summary {
                flex-basis: 86px;
                width: 86px;
                height: 86px;
            }

            .weather-header {
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<article class="weather-card is-loading" id="weather-card" aria-live="polite" aria-busy="true">
    <div class="weather-summary" id="weather-summary" aria-label="Current weather">
        <span class="weather-symbol skeleton skeleton-symbol" id="weather-symbol" aria-hidden="true"></span>
        <strong class="summary-temperature skeleton skeleton-summary-temperature" id="summary-temperature"></strong>
        <span class="weather-summary-label">Now</span>
    </div>

    <div class="weather-info">
        <header class="weather-header">
            <div class="weather-heading-copy">
                <div class="label">Weather</div>
                <h1 class="weather-title" id="location-name"><span class="skeleton skeleton-title"></span></h1>
                <p class="location-details" id="location-details"><span class="skeleton skeleton-details"></span></p>
            </div>
            <time class="weather-time" id="current-time" datetime=""><span class="skeleton skeleton-time"></span></time>
        </header>

        <section class="weather-current" aria-labelledby="current-weather-title">
            <div class="section-heading">
                <h2 class="section-title" id="current-weather-title">Current conditions</h2>
            </div>
            <dl class="weather-stats">
                <div class="weather-stat">
                    <dt>Humidity</dt>
                    <dd id="current-humidity"><span class="skeleton skeleton-value"></span></dd>
                </div>
                <div class="weather-stat">
                    <dt>Rain</dt>
                    <dd id="current-rain"><span class="skeleton skeleton-value"></span></dd>
                </div>
                <div class="weather-stat">
                    <dt>Precipitation chance</dt>
                    <dd id="current-precipitation-chance"><span class="skeleton skeleton-value"></span></dd>
                </div>
                <div class="weather-stat">
                    <dt>Wind speed</dt>
                    <dd id="current-wind-speed"><span class="skeleton skeleton-value"></span></dd>
                </div>
            </dl>
        </section>

        <section class="weather-next" aria-labelledby="forecast-weather-title">
            <div class="forecast-heading">
                <h2 class="forecast-title" id="forecast-weather-title">Next hour</h2>
                <time class="forecast-time" id="forecast-time"><span class="skeleton skeleton-forecast-time"></span></time>
            </div>
            <dl class="forecast-stats">
                <div class="forecast-stat">
                    <dt>Temp</dt>
                    <dd id="forecast-temp"><span class="skeleton skeleton-value"></span></dd>
                </div>
                <div class="forecast-stat">
                    <dt>Humidity</dt>
                    <dd id="forecast-humidity"><span class="skeleton skeleton-value"></span></dd>
                </div>
                <div class="forecast-stat">
                    <dt>Rain</dt>
                    <dd id="forecast-rain"><span class="skeleton skeleton-value"></span></dd>
                </div>
                <div class="forecast-stat">
                    <dt>Chance</dt>
                    <dd id="forecast-precipitation-chance"><span class="skeleton skeleton-value"></span></dd>
                </div>
                <div class="forecast-stat">
                    <dt>Wind</dt>
                    <dd id="forecast-wind-speed"><span class="skeleton skeleton-value"></span></dd>
                </div>
            </dl>
        </section>

        <div class="charts-grid" id="charts-grid">
            <section class="chart-card" aria-labelledby="temperature-chart-title">
                <div class="chart-heading">
                    <h2 class="chart-title" id="temperature-chart-title">Temperature forecast</h2>
                    <span class="chart-range">Next 6 hours</span>
                </div>
                <div class="chart-wrap">
                    <span class="skeleton skeleton-chart" id="temperature-chart-skeleton" aria-hidden="true"></span>
                    <canvas class="weather-chart-canvas" id="temperature-chart-canvas" role="img" aria-label="Temperature forecast chart" hidden></canvas>
                </div>
            </section>

            <section class="chart-card" aria-labelledby="rain-chart-title">
                <div class="chart-heading">
                    <h2 class="chart-title" id="rain-chart-title">Rain forecast</h2>
                    <span class="chart-range">Next 6 hours</span>
                </div>
                <div class="chart-wrap">
                    <span class="skeleton skeleton-chart" id="rain-chart-skeleton" aria-hidden="true"></span>
                    <canvas class="weather-chart-canvas" id="rain-chart-canvas" role="img" aria-label="Rain forecast chart" hidden></canvas>
                </div>
            </section>
        </div>

        <p class="chart-fallback" id="chart-fallback" hidden>Charts are unavailable.</p>
        <p class="weather-error" id="weather-error" hidden></p>
        <div class="weather-footer">
            <p class="weather-status" id="weather-status"><span class="skeleton skeleton-status"></span></p>
            <p class="weather-attribution">Weather data by <a href="https://open-meteo.com/" target="_blank" rel="noopener noreferrer">Open-Meteo</a></p>
        </div>
    </div>
</article>

<script>
    (function () {
        'use strict';

        var GEOLOCATION_TIMEOUT = 10000;
        var REFRESH_INTERVAL = 10 * 60 * 1000;
        var weatherCard = document.getElementById('weather-card');
        var weatherSummary = document.getElementById('weather-summary');
        var weatherSymbol = document.getElementById('weather-symbol');
        var summaryTemperature = document.getElementById('summary-temperature');
        var locationName = document.getElementById('location-name');
        var locationDetails = document.getElementById('location-details');
        var currentTime = document.getElementById('current-time');
        var weatherStatus = document.getElementById('weather-status');
        var forecastTime = document.getElementById('forecast-time');
        var chartsGrid = document.getElementById('charts-grid');
        var chartFallback = document.getElementById('chart-fallback');
        var weatherError = document.getElementById('weather-error');
        var temperatureChartCanvas = document.getElementById('temperature-chart-canvas');
        var rainChartCanvas = document.getElementById('rain-chart-canvas');
        var temperatureChartSkeleton = document.getElementById('temperature-chart-skeleton');
        var rainChartSkeleton = document.getElementById('rain-chart-skeleton');
        var activeLocation = null;
        var activeTimezone = null;
        var refreshTimer = null;
        var temperatureChartInstance = null;
        var rainChartInstance = null;

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

        function setText(element, value) {
            if (!element) {
                return;
            }

            element.classList.remove('skeleton');
            element.textContent = value;
        }

        function formatNumber(value, decimals) {
            var number = readNumber(value);

            if (number === null) {
                return 'N/A';
            }

            return number.toLocaleString('en-GB', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }

        function setMetric(id, value, unit, decimals) {
            var element = document.getElementById(id);
            var number = readNumber(value);
            var text = number === null ? 'N/A' : formatNumber(number, decimals) + ' ' + unit;

            setText(element, text);
        }

        function formatClockValue(value) {
            if (typeof value !== 'string') {
                return 'N/A';
            }

            var timePart = value.split('T')[1] || '';

            return timePart.slice(0, 5) || 'N/A';
        }

        function updateClock() {
            var now = new Date();

            if (activeTimezone && typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
                try {
                    setText(currentTime, new Intl.DateTimeFormat('en-GB', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                        timeZone: activeTimezone
                    }).format(now));
                    return;
                } catch (error) {
                    // Fall back to the timestamp returned by the API.
                }
            }

            if (currentTime.getAttribute('datetime')) {
                setText(currentTime, formatClockValue(currentTime.getAttribute('datetime')));
            }
        }

        function setCurrentTime(value) {
            currentTime.setAttribute('datetime', typeof value === 'string' ? value : '');
            setText(currentTime, formatClockValue(value));
            updateClock();
        }

        function fetchJson(url) {
            return fetch(url, {
                headers: {
                    Accept: 'application/json'
                }
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('The weather API returned HTTP ' + response.status + '.');
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
                name: 'Selected location',
                detail: latitude.toFixed(4) + '°, ' + longitude.toFixed(4) + '°'
            };
        }

        function getUrlLocation() {
            var params = new URLSearchParams(window.location.search);
            var locationQuery = (params.get('location') || '').trim();
            var latitude = parseCoordinate(params.get('lat') || params.get('latitude'), -90, 90);
            var longitude = parseCoordinate(params.get('lon') || params.get('lng') || params.get('longitude'), -180, 180);

            if (locationQuery.length > 100) {
                throw new Error('The location parameter is too long.');
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
                    name: 'Selected location',
                    detail: latitude.toFixed(4) + '°, ' + longitude.toFixed(4) + '°'
                };
            }

            if (params.has('lat') || params.has('latitude') || params.has('lon') || params.has('lng') || params.has('longitude')) {
                throw new Error('Use valid coordinates, for example ?lat=52.37&lon=4.90.');
            }

            return null;
        }

        function geocodeLocation(query) {
            var url = new URL('https://geocoding-api.open-meteo.com/v1/search');

            url.searchParams.set('name', query);
            url.searchParams.set('count', '1');
            url.searchParams.set('language', 'en');
            url.searchParams.set('format', 'json');

            return fetchJson(url.toString()).then(function (data) {
                var result = data && Array.isArray(data.results) ? data.results[0] : null;

                if (!result || !isFiniteNumber(Number(result.latitude)) || !isFiniteNumber(Number(result.longitude))) {
                    throw new Error('Location not found: ' + query + '.');
                }

                var detailParts = [result.admin1, result.country];
                var uniqueDetailParts = [];

                detailParts.forEach(function (part) {
                    if (part && uniqueDetailParts.indexOf(part) === -1) {
                        uniqueDetailParts.push(part);
                    }
                });

                return {
                    latitude: Number(result.latitude),
                    longitude: Number(result.longitude),
                    name: result.name || query,
                    detail: uniqueDetailParts.join(' · ')
                };
            });
        }

        function getBrowserLocation() {
            return new Promise(function (resolve, reject) {
                if (!navigator.geolocation) {
                    reject(new Error('This browser does not support automatic location. Use ?location=Amsterdam.'));
                    return;
                }

                navigator.geolocation.getCurrentPosition(function (position) {
                    var latitude = readNumber(position.coords.latitude);
                    var longitude = readNumber(position.coords.longitude);

                    if (latitude === null || longitude === null) {
                        reject(new Error('The browser did not provide valid coordinates.'));
                        return;
                    }

                    resolve({
                        latitude: latitude,
                        longitude: longitude,
                        name: 'Your location',
                        detail: 'Browser location'
                    });
                }, function (error) {
                    var message = 'Automatic location failed';

                    if (error && error.code === error.PERMISSION_DENIED) {
                        message = 'Location access was denied';
                    } else if (error && error.code === error.TIMEOUT) {
                        message = 'The location request timed out';
                    }

                    reject(new Error(message + '. Use ?location=Amsterdam.'));
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
                'wind_speed_10m',
                'weather_code'
            ].join(','));
            url.searchParams.set('hourly', [
                'temperature_2m',
                'relative_humidity_2m',
                'rain',
                'precipitation_probability',
                'wind_speed_10m',
                'weather_code'
            ].join(','));
            url.searchParams.set('forecast_hours', '7');
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
            var forecastIndex = nextIndex >= 0 ? nextIndex : (times.length > 1 ? 1 : 0);

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

        function getWeatherCondition(code) {
            var weatherCode = readNumber(code);

            if (weatherCode === null) {
                return {
                    icon: '•',
                    label: 'Unknown conditions',
                    color: '#777777'
                };
            }

            weatherCode = Math.round(weatherCode);

            if (weatherCode === 0) {
                return { icon: '☀', label: 'Clear sky', color: '#d98b00' };
            }

            if (weatherCode <= 3) {
                return { icon: '☁', label: 'Partly cloudy', color: '#687d91' };
            }

            if (weatherCode <= 48) {
                return { icon: '≋', label: 'Fog', color: '#83909a' };
            }

            if (weatherCode <= 57) {
                return { icon: '☂', label: 'Drizzle', color: '#4a749a' };
            }

            if (weatherCode <= 67 || (weatherCode >= 80 && weatherCode <= 82)) {
                return { icon: '☂', label: 'Rain', color: '#4a749a' };
            }

            if (weatherCode <= 77 || (weatherCode >= 85 && weatherCode <= 86)) {
                return { icon: '❄', label: 'Snow', color: '#6d9fbe' };
            }

            if (weatherCode >= 95) {
                return { icon: 'ϟ', label: 'Thunderstorm', color: '#8d5ba6' };
            }

            return { icon: '☁', label: 'Cloudy', color: '#687d91' };
        }

        function buildChartPoints(data, indexes) {
            var current = data.current;
            var hourly = data.hourly;
            var points = [];
            var currentTemperature = readNumber(current.temperature_2m);
            var times = indexes.times;

            if (currentTemperature !== null) {
                points.push({
                    label: 'Now',
                    temperature: currentTemperature,
                    rain: readNumber(current.rain)
                });
            }

            for (var index = indexes.forecastIndex; index < times.length && points.length < 7; index += 1) {
                var temperature = arrayValue(hourly.temperature_2m, index);

                if (temperature === null) {
                    continue;
                }

                points.push({
                    label: formatClockValue(times[index]),
                    temperature: temperature,
                    rain: arrayValue(hourly.rain, index)
                });
            }

            return points;
        }

        function destroyCharts() {
            if (temperatureChartInstance) {
                temperatureChartInstance.destroy();
                temperatureChartInstance = null;
            }

            if (rainChartInstance) {
                rainChartInstance.destroy();
                rainChartInstance = null;
            }
        }

        function showChartFallback(message) {
            destroyCharts();
            chartsGrid.hidden = true;
            temperatureChartCanvas.hidden = true;
            rainChartCanvas.hidden = true;
            temperatureChartSkeleton.hidden = true;
            rainChartSkeleton.hidden = true;
            chartFallback.textContent = message;
            chartFallback.hidden = false;
        }

        function createLineChart(canvas, skeleton, title, points, valueKey, color, unit, decimals, beginAtZero) {
            if (!window.Chart || !canvas) {
                return null;
            }

            var context = canvas.getContext('2d');

            if (!context) {
                return null;
            }

            try {
                var chart = new window.Chart(context, {
                    type: 'line',
                    data: {
                        labels: points.map(function (point) {
                            return point.label;
                        }),
                        datasets: [{
                            label: title,
                            data: points.map(function (point) {
                                return point[valueKey];
                            }),
                            borderColor: color,
                            backgroundColor: color === '#b64b52' ? 'rgba(182, 75, 82, 0.14)' : 'rgba(74, 116, 154, 0.14)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointHitRadius: 14,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: color,
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: true,
                            spanGaps: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 450
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                displayColors: false,
                                callbacks: {
                                    label: function (context) {
                                        var value = readNumber(context.parsed && context.parsed.y);

                                        if (value === null) {
                                            return title + ': No data';
                                        }

                                        return title + ': ' + formatNumber(value, decimals) + ' ' + unit;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    color: '#888888',
                                    font: {
                                        family: 'Arial, Helvetica, sans-serif',
                                        size: 9
                                    },
                                    maxTicksLimit: 7,
                                    autoSkip: true,
                                    padding: 2
                                }
                            },
                            y: {
                                beginAtZero: beginAtZero,
                                grace: '10%',
                                grid: {
                                    color: '#e7e7e7'
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    color: '#888888',
                                    font: {
                                        family: 'Arial, Helvetica, sans-serif',
                                        size: 9
                                    },
                                    maxTicksLimit: 4,
                                    callback: function (value) {
                                        return formatNumber(value, decimals) + ' ' + unit;
                                    }
                                }
                            }
                        }
                    }
                });

                canvas.hidden = false;
                skeleton.hidden = true;

                return chart;
            } catch (error) {
                return null;
            }
        }

        function renderCharts(points) {
            destroyCharts();

            if (!Array.isArray(points) || points.length < 2) {
                showChartFallback('Not enough forecast data for charts.');
                return;
            }

            chartFallback.hidden = true;
            chartsGrid.hidden = false;
            temperatureChartSkeleton.hidden = false;
            rainChartSkeleton.hidden = false;

            temperatureChartInstance = createLineChart(
                temperatureChartCanvas,
                temperatureChartSkeleton,
                'Temperature',
                points,
                'temperature',
                '#b64b52',
                '°C',
                1,
                false
            );
            rainChartInstance = createLineChart(
                rainChartCanvas,
                rainChartSkeleton,
                'Rain',
                points,
                'rain',
                '#4a749a',
                'mm',
                1,
                true
            );

            if (!temperatureChartInstance || !rainChartInstance) {
                showChartFallback('Charts are unavailable in this browser.');
            }
        }

        function renderWeather(data, location) {
            var current = data && data.current ? data.current : null;
            var hourly = data && data.hourly ? data.hourly : null;

            if (!current || !hourly) {
                throw new Error('The weather API returned no usable data.');
            }

            var indexes = getForecastIndexes(hourly, current);
            var currentTemperature = readNumber(current.temperature_2m);
            var forecastTemperature = arrayValue(hourly.temperature_2m, indexes.forecastIndex);

            if (currentTemperature === null || forecastTemperature === null) {
                throw new Error('The weather API returned no temperature for the next hour.');
            }

            activeTimezone = typeof data.timezone === 'string' ? data.timezone : null;
            locationName.textContent = location.name;
            locationDetails.textContent = location.detail || '';
            locationDetails.hidden = !location.detail;
            weatherCard.classList.remove('is-loading', 'has-error');
            weatherCard.setAttribute('aria-busy', 'false');

            var condition = getWeatherCondition(current.weather_code);
            weatherSymbol.style.color = condition.color;
            setText(weatherSymbol, condition.icon);
            setText(summaryTemperature, formatNumber(currentTemperature, 1) + '°C');
            weatherSummary.setAttribute('aria-label', 'Current weather: ' + formatNumber(currentTemperature, 1) + ' degrees Celsius, ' + condition.label);
            setCurrentTime(current.time);

            setMetric('current-humidity', current.relative_humidity_2m, '%', 0);
            setMetric('current-rain', current.rain, 'mm', 1);
            setMetric('current-precipitation-chance', arrayValue(hourly.precipitation_probability, indexes.currentIndex), '%', 0);
            setMetric('current-wind-speed', current.wind_speed_10m, 'km/h', 0);
            setForecastMetric('forecast', hourly, indexes.forecastIndex);
            setText(forecastTime, formatClockValue(indexes.times[indexes.forecastIndex]));
            setText(weatherStatus, 'Updated ' + formatClockValue(current.time));

            weatherError.hidden = true;
            renderCharts(buildChartPoints(data, indexes));
        }

        function showInitialError(error) {
            var message = error && error.message ? error.message : 'Weather data could not be loaded.';

            weatherCard.classList.remove('is-loading');
            weatherCard.classList.add('has-error');
            weatherCard.setAttribute('aria-busy', 'false');
            setText(locationName, 'Weather unavailable');
            setText(locationDetails, '');
            locationDetails.hidden = true;
            currentTime.setAttribute('datetime', '');
            setText(currentTime, 'N/A');
            setText(weatherStatus, 'Unable to load weather data.');
            setText(weatherSymbol, '!');
            weatherSymbol.style.color = '#b64b52';
            setText(summaryTemperature, 'N/A');
            weatherSummary.setAttribute('aria-label', 'Weather unavailable');

            [
                'current-humidity',
                'current-rain',
                'current-precipitation-chance',
                'current-wind-speed',
                'forecast-temp',
                'forecast-humidity',
                'forecast-rain',
                'forecast-precipitation-chance',
                'forecast-wind-speed'
            ].forEach(function (id) {
                setMetric(id, null, '', 0);
            });

            setText(forecastTime, 'N/A');
            showChartFallback('Charts are unavailable until weather data loads.');
            weatherError.textContent = message;
            weatherError.hidden = false;
        }

        function loadWeather(location, initialLoad) {
            if (initialLoad) {
                setText(weatherStatus, 'Locating weather data...');
            }

            return fetchJson(buildForecastUrl(location)).then(function (data) {
                renderWeather(data, location);
            }).catch(function (error) {
                if (initialLoad) {
                    showInitialError(error);
                } else {
                    setText(weatherStatus, 'Showing the last update; refresh failed.');
                }
            });
        }

        function start() {
            setText(weatherStatus, 'Locating...');

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
