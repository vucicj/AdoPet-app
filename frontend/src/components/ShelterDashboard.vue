<script setup>
const props = defineProps({
  pets: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <div class="shelter-dashboard">
    <section class="hero hero-shelter">
      <div class="hero-content">
        <h1 class="hero-title">Shelter Dashboard</h1>
        <p class="hero-subtitle">Manage your pets and adoption requests</p>
      </div>
    </section>

    <div class="shelter-content">
      <div class="action-bar">
        <button class="primary-btn">+ Add New Pet</button>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">🐾</div>
          <div class="stat-info">
            <h3>Your Pets</h3>
            <p class="stat-number">{{ pets.length }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📋</div>
          <div class="stat-info">
            <h3>Pending Requests</h3>
            <p class="stat-number">8</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-info">
            <h3>Approved</h3>
            <p class="stat-number">24</p>
          </div>
        </div>
      </div>

      <div class="shelter-sections">
        <div class="shelter-section">
          <h2>Your Pets</h2>
          <div class="pets-grid-small">
            <div v-for="pet in pets.slice(0, 6)" :key="pet.id" class="pet-card-small">
              <img :src="pet.image" :alt="pet.name" />
              <div class="pet-card-info">
                <h4>{{ pet.name }}</h4>
                <p>{{ pet.breed }}</p>
                <div class="pet-actions">
                  <button class="btn-edit">Edit</button>
                  <button class="btn-delete">Delete</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="shelter-section">
          <h2>Adoption Requests</h2>
          <div class="requests-list">
            <p class="placeholder-text">No pending requests</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hero-shelter {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

.shelter-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px 24px;
}

.action-bar {
  margin-bottom: 24px;
}

.primary-btn {
  padding: 12px 24px;
  background: #10b981;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
}

.primary-btn:hover {
  background: #059669;
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

.shelter-sections {
  display: grid;
  gap: 24px;
}

.shelter-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.shelter-section h2 {
  margin: 0 0 16px;
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.pets-grid-small {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}

.pet-card-small {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.2s;
}

.pet-card-small:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.pet-card-small img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.pet-card-info {
  padding: 12px;
}

.pet-card-info h4 {
  margin: 0 0 4px;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.pet-card-info p {
  margin: 0 0 12px;
  font-size: 13px;
  color: #6b7280;
}

.pet-actions {
  display: flex;
  gap: 8px;
}

.btn-edit,
.btn-delete {
  flex: 1;
  padding: 6px;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s;
}

.btn-edit {
  background: #3b82f6;
  color: #fff;
}

.btn-delete {
  background: #ef4444;
  color: #fff;
}

.btn-edit:hover,
.btn-delete:hover {
  opacity: 0.8;
}

.requests-list {
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
