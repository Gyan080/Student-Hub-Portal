// ===== Campus Weather & Clock Script (PDF Unit 3 & 4) =====
const apiUrl = "https://api.open-meteo.com/v1/forecast?latitude=22.6012&longitude=72.8201&current=temperature_2m,relative_humidity_2m,wind_speed_10m&timezone=auto";

async function getWeather() {
  try {
    const response = await fetch(apiUrl);
    if (!response.ok) {
      throw new Error("Weather request failed with status: " + response.status);
    }

    const data = await response.json();

    const tempEl = document.getElementById("temp");
    const windEl = document.getElementById("wind");
    const humidityEl = document.getElementById("humidity");

    if (tempEl) tempEl.textContent = data.current.temperature_2m;
    if (windEl) windEl.textContent = data.current.wind_speed_10m;
    if (humidityEl) humidityEl.textContent = data.current.relative_humidity_2m;
  } catch (error) {
    console.warn("Live weather fetch unavailable, displaying fallback values.", error);
    const tempEl = document.getElementById("temp");
    const windEl = document.getElementById("wind");
    const humidityEl = document.getElementById("humidity");
    if (tempEl) tempEl.textContent = "28.5";
    if (windEl) windEl.textContent = "12";
    if (humidityEl) humidityEl.textContent = "54";
  }
}

function updateTime() {
  const timeEl = document.getElementById("time");
  if (timeEl) {
    timeEl.textContent = new Date().toLocaleTimeString();
  }
}

// Initial calls & timers (PDF Unit 3.6)
getWeather();
updateTime();
setInterval(getWeather, 600000); // 10 minutes
setInterval(updateTime, 1000); // 1 second