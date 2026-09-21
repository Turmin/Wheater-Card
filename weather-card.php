<?php
function isDarkModeRequested(): bool {
    if (!isset($_GET['darkmode']) || is_array($_GET['darkmode'])) {
        return false;
    }

    $value = strtolower(trim((string) $_GET['darkmode']));

    return $value === '' || in_array($value, ['1', 'true', 'yes', 'on'], true);
}

$darkMode = isDarkModeRequested();
?>
<!doctype html>
<html lang="en" class="<?= $darkMode ? 'dark-mode' : '' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weather</title>
    <link rel="stylesheet" href="static/css/style.css">
</head>
<body>

<article class="weather-card is-loading" id="weather-card" aria-live="polite" aria-busy="true">
    <div class="weather-summary" id="weather-summary" aria-label="Current weather">
        <span class="weather-symbol skeleton skeleton-symbol" id="weather-symbol" aria-hidden="true"></span>
        <strong class="summary-temperature" id="summary-temperature">
            <span class="summary-temperature-value skeleton skeleton-summary-temperature" id="summary-temperature-value"></span>
            <span class="summary-temperature-unit" id="summary-temperature-unit" hidden>°C</span>
        </strong>
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
            <h2 class="forecast-title" id="forecast-weather-title">Next hour</h2>
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
                    <span class="chart-range">Next 6 hours · 15 min</span>
                </div>
                <div class="chart-wrap">
                    <span class="skeleton skeleton-chart" id="temperature-chart-skeleton" aria-hidden="true"></span>
                    <canvas class="weather-chart-canvas" id="temperature-chart-canvas" role="img" aria-label="Temperature forecast chart" hidden></canvas>
                </div>
            </section>

            <section class="chart-card" aria-labelledby="rain-chart-title">
                <div class="chart-heading">
                    <h2 class="chart-title" id="rain-chart-title">Rain forecast</h2>
                    <span class="chart-range">Next 6 hours · 15 min</span>
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

<script src="static/vendor/chart.js/4.5.1/chart.umd.min.js"></script>
<script src="static/js/weather-card.js"></script>

</body>
</html>
