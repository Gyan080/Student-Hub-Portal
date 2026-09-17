const apiUrl = "https://api.open-meteo.com/v1/forecast?latitude=22.6012&longitude=172.8201&current=temperature_2m,relative_humidity_2m,wind_speed_10m&timezone=auto";

async function getWeather() {
    try {
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error("Weather request failed");
        }

        const data = await response.json();

        document.getElementById("temp").textContent = data.current.temperature_2m;
        document.getElementById("wind").textContent = data.current.wind_speed_10m;
        document.getElementById("humidity").textContent = data.current.relative_humidity_2m;
    } catch (error) {
        console.error("Error loading weather details:", error);
        document.getElementById("weather-box").innerHTML = "<p>Weather data unavailable.</p>";
    }
}

function updateTime() {
    document.getElementById("time").textContent = new Date().toLocaleTimeString();
}

getWeather();
updateTime();
setInterval(getWeather, 600000);
setInterval(updateTime, 1000);