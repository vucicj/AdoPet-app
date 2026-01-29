<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const props = defineProps({
  pets: {
    type: Array,
    default: () => []
  }
})

const filters = ref({
  petType: [],
  age: '',
  size: '',
  location: ''
})

const userApplications = ref([])

const availablePets = computed(() => {
  // Filter out pets that have approved applications
  return props.pets.filter(pet => {
    const hasApprovedApplication = userApplications.value.some(
      app => app.pet_id === pet.id && app.status === 'approved'
    )
    return !hasApprovedApplication
  })
})

const clearFilters = () => {
  filters.value = {
    petType: [],
    age: '',
    size: '',
    location: ''
  }
}

const applyForPet = (petId) => {
  router.push(`/apply/${petId}`)
}

const viewDetails = (petId) => {
  router.push(`/pets/${petId}`)
}

onMounted(async () => {
  // Fetch user applications to check which pets are approved
  try {
    const token = localStorage.getItem('token')
    if (token) {
      const response = await fetch('http://localhost:8000/api/applications', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
      if (response.ok) {
        userApplications.value = await response.json()
      }
    }
  } catch (error) {
    console.error('Failed to fetch applications:', error)
  }
})
</script>

<template>
  <div class="user-dashboard">
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
          <h2>Showing {{ availablePets.length }} pets</h2>
        </div>

        <div class="pets-grid">
          <div v-for="pet in availablePets" :key="pet.id" class="pet-card">
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
              <div class="pet-actions">
                <button class="details-btn" @click="viewDetails(pet.id)">Details</button>
                <button class="adopt-btn" @click="applyForPet(pet.id)">Adopt Me</button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.hero {
  background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%);
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

.pet-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-top: 12px;
}

.details-btn {
  padding: 12px;
  background: #f3f4f6;
  color: #1f2937;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s, border-color 0.2s;
}

.details-btn:hover {
  background: #e5e7eb;
  border-color: #d1d5db;
}

.adopt-btn {
  padding: 12px;
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
  .hero-title {
    font-size: 32px;
  }

  .pets-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  }
}
</style>
