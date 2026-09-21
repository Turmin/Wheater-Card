(function () {
    'use strict';

    var GEOLOCATION_TIMEOUT = 10000;
    var FORECAST_CACHE_TTL = 10 * 60 * 1000;
    var FORECAST_MAX_STALE_AGE = 60 * 60 * 1000;
    var GEOCODING_CACHE_TTL = 24 * 60 * 60 * 1000;
    var CACHE_PREFIX = 'weather-card:v2:';
    var MINUTELY_FORECAST_POINTS = 25;
    var REFRESH_INTERVAL = FORECAST_CACHE_TTL;
    var weatherCard = document.getElementById('weather-card');
    var weatherSummary = document.getElementById('weather-summary');
    var weatherSymbol = document.getElementById('weather-symbol');
    var summaryTemperatureValue = document.getElementById('summary-temperature-value');
    var summaryTemperatureUnit = document.getElementById('summary-temperature-unit');
    var locationName = document.getElementById('location-name');
    var locationDetails = document.getElementById('location-details');
    var currentTime = document.getElementById('current-time');
    var weatherStatus = document.getElementById('weather-status');
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
    var darkMode = document.documentElement.classList.contains('dark-mode');

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
            cache: 'no-store',
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

    function getCacheStorage() {
        try {
            if (window.localStorage && typeof window.localStorage.getItem === 'function') {
                return window.localStorage;
            }
        } catch (error) {
            // Storage can be unavailable in private or restricted browsing contexts.
        }

        return null;
    }

    function removeCacheEntry(key) {
        var storage = getCacheStorage();

        if (!storage || !key) {
            return;
        }

        try {
            storage.removeItem(key);
        } catch (error) {
            // Ignore storage failures and continue without a cache.
        }
    }

    function readCacheEntry(key, maxAge) {
        var storage = getCacheStorage();
        var rawEntry;
        var entry;
        var age;

        if (!storage || !key) {
            return null;
        }

        try {
            rawEntry = storage.getItem(key);

            if (!rawEntry) {
                return null;
            }

            entry = JSON.parse(rawEntry);

            if (!entry || !isFiniteNumber(entry.storedAt) || !Object.prototype.hasOwnProperty.call(entry, 'data')) {
                removeCacheEntry(key);
                return null;
            }

            age = Date.now() - entry.storedAt;

            if (age < 0 || age > maxAge) {
                removeCacheEntry(key);
                return null;
            }

            entry.age = age;
            return entry;
        } catch (error) {
            removeCacheEntry(key);
            return null;
        }
    }

    function writeCacheEntry(key, data) {
        var storage = getCacheStorage();

        if (!storage || !key) {
            return;
        }

        try {
            storage.setItem(key, JSON.stringify({
                storedAt: Date.now(),
                data: data
            }));
        } catch (error) {
            // Ignore quota and storage failures; the API remains the source of truth.
        }
    }

    function getGeocodingCacheKey(query) {
        return CACHE_PREFIX + 'geocode:' + encodeURIComponent(query.trim().toLowerCase());
    }

    function getForecastCacheKey(location) {
        var latitude = readNumber(location && location.latitude);
        var longitude = readNumber(location && location.longitude);

        if (latitude === null || longitude === null) {
            return null;
        }

        return CACHE_PREFIX + 'forecast:' + latitude.toFixed(4) + ':' + longitude.toFixed(4);
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
        var cacheKey = getGeocodingCacheKey(query);
        var cached = readCacheEntry(cacheKey, GEOCODING_CACHE_TTL);

        if (cached && cached.data && isFiniteNumber(Number(cached.data.latitude)) && isFiniteNumber(Number(cached.data.longitude))) {
            return Promise.resolve(cached.data);
        }

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

            var location = {
                latitude: Number(result.latitude),
                longitude: Number(result.longitude),
                name: result.name || query,
                detail: uniqueDetailParts.join(' · ')
            };

            writeCacheEntry(cacheKey, location);

            return location;
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
        url.searchParams.set('minutely_15', [
            'temperature_2m',
            'rain'
        ].join(','));
        url.searchParams.set('forecast_hours', '7');
        url.searchParams.set('forecast_minutely_15', String(MINUTELY_FORECAST_POINTS));
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
        var minutely = data.minutely_15;
        var minutelyTimes = minutely && Array.isArray(minutely.time) ? minutely.time : [];

        if (minutelyTimes.length > 0) {
            for (var minutelyIndex = 0; minutelyIndex < minutelyTimes.length && points.length < MINUTELY_FORECAST_POINTS; minutelyIndex += 1) {
                var minutelyTemperature = arrayValue(minutely.temperature_2m, minutelyIndex);

                if (minutelyTemperature === null) {
                    continue;
                }

                points.push({
                    label: points.length === 0 ? 'Now' : formatClockValue(minutelyTimes[minutelyIndex]),
                    temperature: minutelyTemperature,
                    rain: arrayValue(minutely.rain, minutelyIndex)
                });
            }

            if (points.length >= 2) {
                return points;
            }

            points = [];
        }

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
                        pointBackgroundColor: darkMode ? '#1b1f24' : '#ffffff',
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
                                color: darkMode ? '#aeb6bf' : '#888888',
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
                                color: darkMode ? '#3b424a' : '#e7e7e7'
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                color: darkMode ? '#aeb6bf' : '#888888',
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
        setText(summaryTemperatureValue, formatNumber(currentTemperature, 1));
        summaryTemperatureUnit.hidden = false;
        weatherSummary.setAttribute('aria-label', 'Current weather: ' + formatNumber(currentTemperature, 1) + ' degrees Celsius, ' + condition.label);
        setCurrentTime(current.time);

        setMetric('current-humidity', current.relative_humidity_2m, '%', 0);
        setMetric('current-rain', current.rain, 'mm', 1);
        setMetric('current-precipitation-chance', arrayValue(hourly.precipitation_probability, indexes.currentIndex), '%', 0);
        setMetric('current-wind-speed', current.wind_speed_10m, 'km/h', 0);
        setForecastMetric('forecast', hourly, indexes.forecastIndex);
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
        setText(summaryTemperatureValue, 'N/A');
        summaryTemperatureUnit.hidden = true;
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

        showChartFallback('Charts are unavailable until weather data loads.');
        weatherError.textContent = message;
        weatherError.hidden = false;
    }

    function loadWeather(location, initialLoad) {
        var cacheKey = getForecastCacheKey(location);
        var cached = cacheKey ? readCacheEntry(cacheKey, FORECAST_MAX_STALE_AGE) : null;
        var usedCachedData = false;

        if (cached) {
            try {
                renderWeather(cached.data, location);
                usedCachedData = true;
            } catch (error) {
                removeCacheEntry(cacheKey);
            }
        }

        if (usedCachedData && cached.age < FORECAST_CACHE_TTL) {
            return Promise.resolve();
        }

        if (usedCachedData) {
            setText(weatherStatus, 'Refreshing weather data...');
        } else if (initialLoad) {
            setText(weatherStatus, 'Loading weather data...');
        }

        return fetchJson(buildForecastUrl(location)).then(function (data) {
            renderWeather(data, location);
            writeCacheEntry(cacheKey, data);
        }).catch(function (error) {
            if (usedCachedData) {
                setText(weatherStatus, 'Showing cached weather; refresh failed.');
            } else if (initialLoad) {
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
