<template>
  <div class="p-4 space-x-4">
    <label>
      <input type="checkbox" v-model="showTemperature" @change="toggleTemperatureLayer" />
      Show Temperature
    </label>
    <label>
      <input type="checkbox" v-model="showWind" @change="toggleWindLayer" />
      Show Wind
    </label>
  </div>
</template>

<script>
import L from 'leaflet'

export default {
  name: 'WeatherOverlayControls',
  props: {
    map: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      temperatureLayer: null,
      windLayer: null,
      showTemperature: false,
      showWind: false,
      apiKey: 'YOUR_API_KEY' // Replace with your OpenWeatherMap API key
    }
  },
  methods: {
    toggleTemperatureLayer() {
      const url = `https://tile.openweathermap.org/map/temp_new/{z}/{x}/{y}.png?appid=${this.apiKey}`

      if (this.showTemperature && this.map) {
        this.temperatureLayer = L.tileLayer(url)
        this.temperatureLayer.addTo(this.map)
      } else if (this.temperatureLayer) {
        this.map.removeLayer(this.temperatureLayer)
      }
    },
    toggleWindLayer() {
      const url = `https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=${this.apiKey}`

      if (this.showWind && this.map) {
        this.windLayer = L.tileLayer(url)
        this.windLayer.addTo(this.map)
      } else if (this.windLayer) {
        this.map.removeLayer(this.windLayer)
      }
    }
  }
}
</script>
