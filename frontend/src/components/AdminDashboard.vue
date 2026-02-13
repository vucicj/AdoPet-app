<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
  totalAccounts: 0,
  totalShelters: 0,
  totalPets: 0,
  totalAdoptions: 0
})
const recentAccounts = ref([])
const pets = ref([])
const allAccounts = ref([])
const loading = ref(true)
const error = ref('')
const viewingPets = ref(false)
const viewingAccounts = ref(false)
const petsLoading = ref(false)
const accountsLoading = ref(false)

const fetchStats = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://localhost:8000/api/admin/stats', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      stats.value = await response.json()
    } else {
      error.value = 'Failed to load statistics'
    }
  } catch (err) {
    console.error('Error fetching stats:', err)
    error.value = 'Error loading statistics'
  }
}

const fetchRecentAccounts = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://localhost:8000/api/admin/recent-accounts', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      recentAccounts.value = await response.json()
    }
  } catch (err) {
    console.error('Error fetching recent accounts:', err)
  }
}

const fetchPets = async () => {
  petsLoading.value = true
  try {
    const response = await fetch('http://localhost:8000/api/pets', {
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      pets.value = await response.json()
      viewingPets.value = true
    }
  } catch (err) {
    console.error('Error fetching pets:', err)
  } finally {
    petsLoading.value = false
  }
}

const handlePetsClick = async () => {
  viewingAccounts.value = false
  viewingPets.value = true
  await fetchPets()
}

const fetchAllAccounts = async () => {
  accountsLoading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://localhost:8000/api/admin/recent-accounts?limit=1000', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      allAccounts.value = await response.json()
    }
  } catch (err) {
    console.error('Error fetching all accounts:', err)
  } finally {
    accountsLoading.value = false
  }
}

const handleAccountsClick = async () => {
  viewingPets.value = false
  viewingAccounts.value = true
  await fetchAllAccounts()
}

onMounted(async () => {
  await Promise.all([fetchStats(), fetchRecentAccounts()])
  loading.value = false
})
</script>

<template>
  <div class="admin-dashboard">
    <section class="hero hero-admin">
      <div class="hero-content">
        <h1 class="hero-title">Admin Dashboard</h1>
        <p class="hero-subtitle">Manage users, shelters, and oversee all adoptions</p>
      </div>
    </section>

    <div v-if="error" class="error-banner">
      {{ error }}
    </div>

    <div v-if="loading" class="loading">
      <p>Loading admin data...</p>
    </div>

    <div v-else class="admin-content">
      <div class="stats-grid">
        <div class="stat-card clickable-card" @click="handleAccountsClick">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <h3>Total Accounts</h3>
            <p class="stat-number">{{ stats.totalAccounts }}</p>
          </div>
        </div>
        <div class="stat-card clickable-card" @click="handlePetsClick">
          <div class="stat-icon">🐾</div>
          <div class="stat-info">
            <h3>Total Pets</h3>
            <p class="stat-number">{{ stats.totalPets }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">❤️</div>
          <div class="stat-info">
            <h3>Adoptions</h3>
            <p class="stat-number">{{ stats.totalAdoptions }}</p>
          </div>
        </div>
      </div>

      <div class="admin-sections">
        <div v-if="viewingPets" class="admin-section">
          <div class="section-header">
            <h2>Total Pets</h2>
            <button class="close-btn" @click="viewingPets = false">✕</button>
          </div>
          <div class="admin-table">
            <div v-if="petsLoading" class="loading-small">Loading pets...</div>
            <table v-else-if="pets.length > 0">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Breed</th>
                  <th>Age</th>
                  <th>Location</th>
                  <th>Status</th>
                  <th>Shelter</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="pet in pets" :key="pet.id">
                  <td>{{ pet.name }}</td>
                  <td>{{ pet.breed }}</td>
                  <td>{{ pet.age }}</td>
                  <td>{{ pet.location }}</td>
                  <td>
                    <span class="status-badge" :class="`status-${pet.status}`">
                      {{ pet.status }}
                    </span>
                  </td>
                  <td>{{ pet.shelter?.name || 'N/A' }}</td>
                </tr>
              </tbody>
            </table>
            <p v-else class="placeholder-text">No pets found</p>
          </div>
        </div>

        <div v-if="viewingAccounts" class="admin-section">
          <div class="section-header">
            <h2>Total Accounts</h2>
            <button class="close-btn" @click="viewingAccounts = false">✕</button>
          </div>
          <div class="admin-table">
            <div v-if="accountsLoading" class="loading-small">Loading accounts...</div>
            <table v-else-if="allAccounts.length > 0">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="account in allAccounts" :key="account.id">
                  <td>{{ account.name }}</td>
                  <td>{{ account.email }}</td>
                  <td>
                    <span class="role-badge" :class="`role-${account.role}`">
                      {{ account.role.toUpperCase() }}
                    </span>
                  </td>
                  <td>{{ account.created_at }}</td>
                </tr>
              </tbody>
            </table>
            <p v-else class="placeholder-text">No accounts found</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hero-admin {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  padding: 60px 24px;
  text-align: center;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

.hero-title {
  color: #fff;
  font-size: 42px;
  font-weight: 700;
  margin: 0 0 12px;
}

.hero-subtitle {
  color: rgba(255, 255, 255, 0.95);
  font-size: 18px;
  margin: 0;
}

.admin-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-card.clickable-card {
  cursor: pointer;
}

.stat-card.clickable-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  background: #fff8f8;
}

.stat-icon {
  font-size: 36px;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  border-radius: 12px;
}

.stat-info h3 {
  margin: 0;
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.stat-number {
  margin: 4px 0 0;
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
}

.admin-sections {
  display: grid;
  gap: 24px;
}

.admin-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.admin-section h2 {
  margin: 0 0 16px;
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-header h2 {
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #6b7280;
  padding: 0;
  line-height: 1;
}

.close-btn:hover {
  color: #1f2937;
}

.admin-table {
  overflow-x: auto;
}

.admin-table table {
  width: 100%;
  border-collapse: collapse;
}

.admin-table th {
  text-align: left;
  padding: 12px;
  background: #f9fafb;
  font-weight: 600;
  color: #374151;
  font-size: 14px;
  border-bottom: 2px solid #e5e7eb;
}

.admin-table td {
  padding: 12px;
  border-bottom: 1px solid #e5e7eb;
  color: #4b5563;
  font-size: 14px;
}

.admin-table tbody tr:hover {
  background: #f9fafb;
}

.role-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.role-user {
  background: #dbeafe;
  color: #1e40af;
}

.role-shelter {
  background: #d1fae5;
  color: #065f46;
}

.role-admin {
  background: #fee2e2;
  color: #991b1b;
}

.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: capitalize;
}

.status-available {
  background: #d1fae5;
  color: #065f46;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-adopted {
  background: #dbeafe;
  color: #1e40af;
}

.error-banner {
  background: #fee;
  color: #c00;
  padding: 12px 24px;
  text-align: center;
  font-size: 14px;
}

.loading {
  text-align: center;
  padding: 48px;
  color: #6b7280;
}

.placeholder-text {
  color: #9ca3af;
  font-size: 14px;
  text-align: center;
  padding: 48px;
}

.loading-small {
  text-align: center;
  padding: 24px;
  color: #6b7280;
  font-size: 14px;
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 32px;
  }
}
</style>
