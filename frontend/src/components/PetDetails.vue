<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const pet = ref(null)
const loading = ref(true)
const error = ref('')
const userRole = ref('')

const getImagePath = (imageName) => {
  if (!imageName) return null
  if (imageName.startsWith('http://') || imageName.startsWith('https://') || imageName.startsWith('data:') || imageName.startsWith('/')) {
    return imageName
  }
  try {
    return new URL(`../assets/images/${imageName}`, import.meta.url).href
  } catch {
    return null
  }
}

const goBack = () => {
  router.push('/dashboard')
}

const adoptPet = () => {
  router.push(`/apply/${pet.value.id}`)
}

onMounted(async () => {
  const userData = localStorage.getItem('user')
  if (userData) {
    userRole.value = JSON.parse(userData).role || 'user'
  }

  try {
    const { petId } = route.params
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/pets/${petId}`)

    if (!response.ok) {
      error.value = 'Failed to load pet details'
      return
    }

    const data = await response.json()
    pet.value = {
      ...data,
      image: getImagePath(data.image) || getImagePath('dog1.jpg')
    }
  } catch (err) {
    console.error('Error loading pet details:', err)
    error.value = 'Error loading pet details'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="pet-details-page">
    <div class="top-bar">
      <button class="back-btn" @click="goBack">← Back to Dashboard</button>
    </div>

    <div v-if="loading" class="loading">Loading pet details...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else-if="pet" class="details-grid">
      <div class="gallery">
        <div class="main-image">
          <img :src="pet.image" :alt="pet.name" />
        </div>
        <div class="status-row">
          <span class="status-badge" :class="`status-${pet.status}`">{{ pet.status }}</span>
        </div>
      </div>

      <div class="info-panel">
        <div class="title-row">
          <div>
            <h1 class="pet-name">{{ pet.name }}</h1>
            <p class="pet-breed">{{ pet.breed }}</p>
          </div>
        </div>

        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-label">Age</div>
            <div class="stat-value">{{ pet.age }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Gender</div>
            <div class="stat-value">{{ pet.gender }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Location</div>
            <div class="stat-value">{{ pet.location }}</div>
          </div>
        </div>

        <div class="section-card">
          <h3>About {{ pet.name }}</h3>
          <p>
            {{ pet.name }} is a wonderful {{ pet.species || pet.breed }} looking for a loving forever home.
            Currently available for adoption at a shelter near {{ pet.location }}.
          </p>
        </div>

        <div class="shelter-card" v-if="pet.shelter">
          <div class="shelter-title">{{ pet.shelter.name }}</div>
          <div class="shelter-location">📍 {{ pet.location }}</div>
        </div>
        <div class="shelter-card" v-else>
          <div class="shelter-title">Local Shelter</div>
          <div class="shelter-location">📍 {{ pet.location }}</div>
        </div>

        <button
          v-if="pet.status === 'available' && (userRole === 'user' || userRole === 'admin')"
          class="adopt-btn"
          @click="adoptPet"
        >
          Adopt {{ pet.name }}
        </button>
        <div v-else-if="pet.status === 'pending'" class="status-notice pending-notice">
          This pet has a pending adoption application.
        </div>
        <div v-else-if="pet.status === 'adopted'" class="status-notice adopted-notice">
          This pet has already been adopted.
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.pet-details-page {
  min-height: 100vh;
  background: #f8fafc;
  padding: 2rem;
}

.top-bar {
  max-width: 1200px;
  margin: 0 auto 1.5rem;
  display: flex;
  justify-content: flex-start;
}

.back-btn {
  background: white;
  border: 1px solid #e5e7eb;
  color: #6b7280;
  font-size: 0.9rem;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s;
}

.back-btn:hover {
  color: #7c3aed;
  border-color: #7c3aed;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.status-row {
  margin-top: 0.75rem;
}

.status-badge {
  display: inline-block;
  padding: 0.35rem 0.85rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 700;
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

.adopt-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.adopt-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(124, 58, 237, 0.4);
}

.status-notice {
  padding: 1rem;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 500;
  text-align: center;
}

.pending-notice {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
}

.adopted-notice {
  background: #dbeafe;
  color: #1e40af;
  border: 1px solid #bfdbfe;
}

.details-grid {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  gap: 2rem;
}

.gallery {
  display: grid;
}

.main-image {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  line-height: 0;
  height: 420px;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  transform: scale(1.08);
  display: block;
  vertical-align: top;
}

.info-panel {
  display: grid;
  gap: 1.5rem;
}

.title-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.pet-name {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
  color: #111827;
}

.pet-breed {
  margin: 0.25rem 0 0;
  color: #6b7280;
  font-weight: 600;
}



.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.stat-card {
  background: #f1f5f9;
  border-radius: 14px;
  padding: 1rem;
  text-align: center;
}

.stat-label {
  font-size: 0.85rem;
  color: #6b7280;
  font-weight: 600;
}

.stat-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: #111827;
  margin-top: 0.25rem;
}

.section-card {
  background: white;
  padding: 1.25rem;
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
}

.section-card h3 {
  margin: 0 0 0.75rem;
  color: #111827;
}

.section-card p {
  margin: 0 0 1rem;
  color: #6b7280;
  line-height: 1.6;
}

.tag-row {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.tag {
  background: #ede9fe;
  color: #6d28d9;
  padding: 0.4rem 0.8rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
}

.health-list {
  margin: 0;
  padding-left: 1.2rem;
  color: #6b7280;
}

.health-list li {
  margin-bottom: 0.5rem;
}

.shelter-card {
  background: linear-gradient(135deg, #c7b5f0, #d6c7f7);
  padding: 1rem 1.25rem;
  border-radius: 16px;
  color: #4c1d95;
  font-weight: 600;
}

.shelter-title {
  font-size: 1rem;
}

.shelter-location {
  font-size: 0.85rem;
  opacity: 0.9;
}



.loading,
.error {
  max-width: 1200px;
  margin: 2rem auto;
  background: white;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  color: #6b7280;
}

@media (max-width: 1024px) {
  .details-grid {
    grid-template-columns: 1fr;
  }

  .main-image img {
    height: 320px;
  }
}

@media (max-width: 640px) {
  .pet-details-page {
    padding: 1rem;
  }

  .stats-row {
    grid-template-columns: 1fr;
  }
}
</style>
