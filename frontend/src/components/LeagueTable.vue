<template>
  <div>
    <div v-if="isLoading" class="fixed inset-0 bg-white bg-opacity-70 flex items-center justify-center z-50">
      <div class="loader"></div>
    </div>

    <div class="flex flex-wrap gap-8" v-else>
      <!-- League Table -->
      <div class="flex-1 min-w-[300px]">
        <h2>📋 League Table</h2>

        <div class="mb-4 space-x-2">
          <button @click="simulateWeek">▶️ Play Next Week</button>
          <button @click="simulateSeason">🏑 Play Full Season</button>
          <button @click="refreshAll">🔄 Refresh Table</button>
        </div>

        <table v-if="table.length" class="styled-table">
          <thead>
          <tr>
            <th>Team</th>
            <th>Pts</th>
            <th>W</th>
            <th>D</th>
            <th>L</th>
            <th>GF</th>
            <th>GA</th>
            <th>GD</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="team in sortedTable" :key="team.teamName">
            <td>{{ team.teamName }}</td>
            <td>{{ team.points }}</td>
            <td>{{ team.wins }}</td>
            <td>{{ team.draws }}</td>
            <td>{{ team.losses }}</td>
            <td>{{ team.goalsFor }}</td>
            <td>{{ team.goalsAgainst }}</td>
            <td>{{ team.goalDifference }}</td>
          </tr>
          </tbody>
        </table>
        <p v-else>No table data found.</p>

        <div>
          <h3>🏆 Championship Predictions</h3>
          <table class="styled-table" v-if="predictions.length">
            <thead>
            <tr>
              <th>Team</th>
              <th>Win Chance</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="p in predictions" :key="p.teamName">
              <td>{{ p.teamName }}</td>
              <td>{{ (p.winProbability * 100).toFixed(1) }}%</td>
            </tr>
            </tbody>
          </table>
          <p v-else>No predictions yet.</p>
        </div>
      </div>

      <!-- Match Results -->
      <div class="flex-1 min-w-[300px]">
        <h2>🗕️ Match Results</h2>

        <div class="flex items-center gap-2 mb-2">
          <span>Select week:</span>
          <select v-model.number="visibleWeek" @change="updateVisibleMatches">
            <option v-for="n in maxWeek" :key="n" :value="n">Week {{ n }}</option>
          </select>
        </div>

        <table v-if="matches.length" class="styled-table">
          <thead>
          <tr>
            <th>Home</th>
            <th>Score</th>
            <th>Away</th>
            <th>Week</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="match in matches" :key="match.id">
            <td>{{ match.homeTeamName ?? '??' }}</td>
            <td>{{ match.homeGoals }} - {{ match.awayGoals }}</td>
            <td>{{ match.awayTeamName ?? '??' }}</td>
            <td>{{ match.week }}</td>
          </tr>
          </tbody>
        </table>
        <p v-else>No matches to display.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const table = ref([])
const predictions = ref([])
const matches = ref([])
const allMatches = ref([])

const isLoading = ref(true)
const currentMode = ref('week')
const currentWeek = ref(1)
const visibleWeek = ref(0)
const maxWeek = ref(1)

const fetchTable = async () => {
  const res = await axios.get('/api/table')
  table.value = res.data
}

const fetchPredictions = async () => {
  const res = await axios.get('/api/predict/chances')
  predictions.value = res.data
}

const fetchCurrentWeek = async () => {
  const res = await axios.get('/api/matches')
  const playedWeeks = res.data
      .filter(m => m.homeGoals !== null && m.awayGoals !== null)
      .map(m => m.week)

  currentWeek.value = playedWeeks.length ? Math.max(...playedWeeks) + 1 : 1
  visibleWeek.value = playedWeeks.length ? Math.max(...playedWeeks) : 0
}

const refreshAll = async () => {
  try {
    isLoading.value = true
    await Promise.all([
      fetchTable(),
      fetchPredictions(),
      fetchCurrentWeek()
    ])
    const res = await axios.get('/api/matches')
    allMatches.value = res.data
    maxWeek.value = Math.max(...res.data.map(m => m.week) ?? [0])
    updateVisibleMatches()
  } catch (error) {
    console.error('Failed to refresh data:', error)
  } finally {
    isLoading.value = false
  }
}

const simulateWeek = async () => {
  await axios.post(`/api/simulate/week/${currentWeek.value}`)
  await refreshAll()
  currentWeek.value++
  currentMode.value = 'season'
}

const simulateSeason = async () => {
  await axios.post('/api/simulate/season')
  await refreshAll()
  visibleWeek.value = 1
  currentMode.value = 'season'
}

const updateVisibleMatches = () => {
  matches.value = allMatches.value.filter(m => m.week === visibleWeek.value)
}

const sortedTable = computed(() =>
    [...table.value].sort((a, b) => {
      if (b.points !== a.points) return b.points - a.points
      if (b.goalDifference !== a.goalDifference) return b.goalDifference - a.goalDifference
      return b.goalsFor - a.goalsFor
    })
)

onMounted(refreshAll)
</script>

<style scoped>
.styled-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
  font-size: 0.95rem;
  font-family: sans-serif;
  min-width: 300px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
}

.styled-table thead tr {
  background-color: #4b5563;
  color: #ffffff;
  text-align: left;
}

.styled-table th,
.styled-table td {
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  text-align: center;
}

.styled-table tbody tr:nth-child(even) {
  background-color: #f9fafb;
}

.styled-table tbody tr:hover {
  background-color: #edf2f7;
  cursor: pointer;
}

.styled-table td:first-child,
.styled-table th:first-child {
  text-align: left;
}

.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #4b5563;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
