<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
  totalAccounts: 0,
  totalShelters: 0,
  totalPets: 0,
  totalAdoptions: 0
})
const recentAccounts = ref([])
const loading = ref(true)
const error = ref('')

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
        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <h3>Total Accounts</h3>
            <p class="stat-number">{{ stats.totalAccounts }}</p>
          </div>
        </div>
        <div class="stat-card">
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
        <div class="admin-section">
          <h2>Recent Accounts</h2>
          <div class="admin-table">
            <table v-if="recentAccounts.length > 0">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="account in recentAccounts" :key="account.id">
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

@media (max-width: 768px) {
  .hero-title {
    font-size: 32px;
  }
}
</style>
