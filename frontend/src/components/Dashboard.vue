<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import dog1 from '@/assets/images/dog1.jpg'
import dog2 from '@/assets/images/dog2.webp'
import cat1 from '@/assets/images/cat1.jpg'
import cat2 from '@/assets/images/cat2.jpg'
import rabbit1 from '@/assets/images/rabbit1.webp'
import rabbit2 from '@/assets/images/rabbit2.webp'
import bird1 from '@/assets/images/bird1.jpg'
import bird2 from '@/assets/images/bird2.jpg'

const router = useRouter()

const pets = ref([
  {
    id: 1,
    name: 'Max',
    image: dog1,
    breed: 'Golden Retriever',
    age: '2 years',
    gender: 'Male',
    location: 'Austin',
    distance: '8 miles'
  },
  {
    id: 2,
    name: 'Luna',
    image: cat2,
    breed: 'Cat',
    age: '1 year',
    gender: 'Female',
    location: 'Austin',
    distance: '5 miles'
  },
  {
    id: 3,
    name: 'Charlie',
    image: dog2,
    breed: 'Beagle',
    age: '3 years',
    gender: 'Male',
    location: 'Chicago',
    distance: '12 miles'
  },
  {
    id: 4,
    name: 'Snowball',
    image: rabbit1,
    breed: 'Persian Cat',
    age: '4 years',
    gender: 'Female',
    location: 'Austin',
    distance: '7 miles'
  },
  {
    id: 5,
    name: 'Buddy',
    image: rabbit2,
    breed: 'Border Collie',
    age: '2 years',
    gender: 'Male',
    location: 'Austin',
    distance: '4 miles'
  },
  {
    id: 6,
    name: 'Misty',
    image: bird1,
    breed: 'Mixed Breed',
    age: '6 months',
    gender: 'Female',
    location: 'Portland',
    distance: '9 miles'
  },
  {
    id: 7,
    name: 'Tweet',
    image: bird2,
    breed: 'Parrot',
    age: '1 year',
    gender: 'Male',
    location: 'Austin',
    distance: '6 miles'
  },
  {
    id: 8,
    name: 'Tweet',
    image: cat1,
    breed: 'Parrot',
    age: '1 year',
    gender: 'Male',
    location: 'Austin',
    distance: '6 miles'
  }
])

const filters = ref({
  petType: [],
  age: '',
  size: '',
  location: ''
})

const user = ref(null)

onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
})

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/')
}

const clearFilters = () => {
  filters.value = {
    petType: [],
    age: '',
    size: '',
    location: ''
  }
}
</script>

<template>
  <div class="dashboard">
    <header class="header">
      <div class="header-content">
        <div class="logo">
          <span class="paw-icon">🐾</span>
          <span class="brand-name">AdoPet</span>
        </div>
        
        <nav class="nav-links">
          <a href="#" class="nav-link">About</a>
        </nav>

        <div class="user-section">
          <span class="user-badge" :class="'badge-' + (user?.role || 'user')">
            {{ user?.role?.toUpperCase() || 'USER' }}
          </span>
          <span class="user-name" v-if="user">{{ user.name }}</span>
          <button class="logout-btn" @click="handleLogout">
            <span class="logout-icon">🚪</span>
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Admin Dashboard -->
    <div v-if="user?.role === 'admin'" class="admin-dashboard">
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
              <p class="stat-number">247</p>
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

    <!-- Shelter Dashboard -->
    <div v-else-if="user?.role === 'shelter'" class="shelter-dashboard">
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
              <p class="stat-number">12</p>
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
              <div v-for="pet in pets.slice(0, 3)" :key="pet.id" class="pet-card-small">
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

    <!-- User Dashboard (Default) -->
    <div v-else class="user-dashboard">
      <section class="hero">
        <div class="hero-content">
          <h1 class="hero-title">Find Your Perfect Companion</h1>
        </div>
      </section>

      <div class="main-container">
        <aside class="filters-sidebar">
          <div class="filters-header">
            <h3>Filters</h3>
            <button class="clear-all" @click="clearFilters">Clear All</button>
          </div>

          <div class="filter-section">
            <h4>Pet Type</h4>
            <label class="checkbox-label">
              <input type="checkbox" value="dogs" v-model="filters.petType" />
              <span>Dogs</span>
            </label>
            <label class="checkbox-label">
              <input type="checkbox" value="cats" v-model="filters.petType" />
              <span>Cats</span>
            </label>
            <label class="checkbox-label">
              <input type="checkbox" value="rabbits" v-model="filters.petType" />
              <span>Rabbits</span>
            </label>
            <label class="checkbox-label">
              <input type="checkbox" value="birds" v-model="filters.petType" />
              <span>Birds</span>
            </label>
          </div>

          <div class="filter-section">
            <h4>Age</h4>
            <label class="radio-label">
              <input type="radio" name="age" value="puppy" v-model="filters.age" />
              <span>Puppy/Kitten</span>
            </label>
            <label class="radio-label">
              <input type="radio" name="age" value="young" v-model="filters.age" />
              <span>Young</span>
            </label>
            <label class="radio-label">
              <input type="radio" name="age" value="adult" v-model="filters.age" />
              <span>Adult</span>
            </label>
            <label class="radio-label">
              <input type="radio" name="age" value="senior" v-model="filters.age" />
              <span>Senior</span>
            </label>
          </div>
        </aside>

        <main class="pets-content">
          <div class="pets-header">
            <h2>Showing 247 pets</h2>
          </div>

          <div class="pets-grid">
            <div v-for="pet in pets" :key="pet.id" class="pet-card">
              <div class="pet-image-wrapper">
                <img :src="pet.image" :alt="pet.name" class="pet-image" />
              </div>
              
              <div class="pet-info">
                <h3 class="pet-name">{{ pet.name }}</h3>
                <p class="pet-details">
                  <span class="detail-item">{{ pet.breed }}</span>
                  <span class="separator">•</span>
                  <span class="detail-item">{{ pet.age }}</span>
                </p>
                <p class="pet-details">
                  <span class="detail-item">📍 {{ pet.location }}</span>
                  <span class="separator">•</span>
                  <span class="detail-item">{{ pet.distance }}</span>
                </p>
                <button class="adopt-btn">Adopt Me</button>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<style scoped>

* {
  box-sizing: border-box;
}

.dashboard {
  min-height: 100vh;
  background: #f8f9fb;
  font-family: 'Poppins', sans-serif;
}

.header {
  background: #fff;
  border-bottom: 1px solid #e8ebf0;
  padding: 16px 0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 22px;
  font-weight: 700;
  color: #8b5cf6;
}

.paw-icon {
  font-size: 28px;
}

.brand-name {
  color: #1f2937;
}

.nav-links {
  display: flex;
  gap: 32px;
}

.nav-link {
  color: #6b7280;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.nav-link:hover {
  color: #8b5cf6;
}

.user-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.badge-user {
  background: #dbeafe;
  color: #1e40af;
}

.badge-shelter {
  background: #d1fae5;
  color: #065f46;
}

.badge-admin {
  background: #fce7f3;
  color: #9f1239;
}

.user-name {
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #ef4444;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
}

.logout-btn:hover {
  background: #dc2626;
}

.logout-icon {
  font-size: 16px;
}

.hero {
  background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%);
  padding: 60px 24px;
  text-align: center;
}

.hero-admin {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.hero-shelter {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
  margin: 0 0 32px;
}

.search-bar {
  display: flex;
  gap: 12px;
  max-width: 600px;
  margin: 0 auto;
}

.search-bar input {
  flex: 1;
  padding: 14px 20px;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  outline: none;
}

.search-btn {
  padding: 14px 32px;
  background: #fff;
  color: #8b5cf6;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transition: transform 0.2s;
}

.search-btn:hover {
  transform: translateY(-2px);
}

.main-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px 24px;
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 32px;
}

.filters-sidebar {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  height: fit-content;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.filters-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.clear-all {
  background: none;
  border: none;
  color: #8b5cf6;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
}

.filter-section {
  margin-bottom: 28px;
  padding-bottom: 28px;
  border-bottom: 1px solid #e8ebf0;
}

.filter-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.filter-section h4 {
  margin: 0 0 14px;
  font-size: 14px;
  font-weight: 700;
  color: #1f2937;
}

.checkbox-label,
.radio-label {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  cursor: pointer;
  color: #4b5563;
  font-size: 14px;
}

.checkbox-label input,
.radio-label input {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.size-buttons {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
}

.size-btn {
  padding: 10px;
  background: #f3f4f6;
  border: 2px solid transparent;
  border-radius: 8px;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s;
}

.size-btn.active,
.size-btn:hover {
  background: #ede9fe;
  border-color: #8b5cf6;
  color: #8b5cf6;
}

.location-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
}

.location-input:focus {
  border-color: #8b5cf6;
}

.pets-content {
  flex: 1;
}

.pets-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.pets-header h2 {
  margin: 0;
  font-size: 22px;
  font-weight: 700;
  color: #1f2937;
}

.sort-select {
  padding: 10px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #4b5563;
  background: #fff;
  cursor: pointer;
  outline: none;
}

.pets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 40px;
}

.pet-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.pet-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.pet-image-wrapper {
  position: relative;
  width: 100%;
  height: 240px;
  overflow: hidden;
}

.pet-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pet-info {
  padding: 16px;
}

.pet-name {
  margin: 0 0 8px;
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.pet-details {
  margin: 0 0 6px;
  font-size: 13px;
  color: #6b7280;
  display: flex;
  align-items: center;
  gap: 6px;
}

.separator {
  color: #d1d5db;
}

.adopt-btn {
  width: 100%;
  padding: 12px;
  margin-top: 12px;
  background: #8b5cf6;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
}

.adopt-btn:hover {
  background: #7c3aed;
}

/* Admin Dashboard Styles */
.admin-content,
.shelter-content {
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

.admin-sections,
.shelter-sections {
  display: grid;
  gap: 24px;
}

.admin-section,
.shelter-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.admin-section h2,
.shelter-section h2 {
  margin: 0 0 16px;
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.admin-table,
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

/* Shelter Dashboard Styles */
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

@media (max-width: 1024px) {
  .main-container {
    grid-template-columns: 1fr;
  }

  .filters-sidebar {
    order: 2;
  }

  .pets-content {
    order: 1;
  }
}

@media (max-width: 768px) {
  .nav-links {
    display: none;
  }

  .hero-title {
    font-size: 32px;
  }

  .search-bar {
    flex-direction: column;
  }

  .pets-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  }
}
</style>
