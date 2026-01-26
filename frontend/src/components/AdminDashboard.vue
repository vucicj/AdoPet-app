<script setup>
const props = defineProps({
  pets: {
    type: Array,
    default: () => []
  }
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

    <div class="admin-content">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <h3>Total Users</h3>
            <p class="stat-number">1,234</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🏠</div>
          <div class="stat-info">
            <h3>Shelters</h3>
            <p class="stat-number">45</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🐾</div>
          <div class="stat-info">
            <h3>Total Pets</h3>
            <p class="stat-number">{{ pets.length }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">❤️</div>
          <div class="stat-info">
            <h3>Adoptions</h3>
            <p class="stat-number">892</p>
          </div>
        </div>
      </div>

      <div class="admin-sections">
        <div class="admin-section">
          <h2>Recent Users</h2>
          <div class="admin-table">
            <p class="placeholder-text">User management coming soon...</p>
          </div>
        </div>
        <div class="admin-section">
          <h2>Recent Shelters</h2>
          <div class="admin-table">
            <p class="placeholder-text">Shelter management coming soon...</p>
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
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.placeholder-text {
  color: #9ca3af;
  font-size: 14px;
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 32px;
  }
}
</style>
