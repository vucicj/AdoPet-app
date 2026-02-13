<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import DashboardHeader from './DashboardHeader.vue'
import UserDashboard from './UserDashboard.vue'
import ShelterDashboard from './ShelterDashboard.vue'
import AdminDashboard from './AdminDashboard.vue'

const router = useRouter()
const user = ref(null)
const pets = ref([])
const loading = ref(true)

const getImagePath = (imageName) => {
  if (!imageName) {
    return null
  }

  if (
    imageName.startsWith('http://') ||
    imageName.startsWith('https://') ||
    imageName.startsWith('data:') ||
    imageName.startsWith('/')
  ) {
    return imageName
  }

  try {
    return new URL(`../assets/images/${imageName}`, import.meta.url).href
  } catch {
    return null
  }
}

const normalizeImageName = (imageValue) => {
  if (!imageValue) {
    return ''
  }

  try {
    if (imageValue.startsWith('http://') || imageValue.startsWith('https://')) {
      const url = new URL(imageValue)
      return url.pathname.split('/').pop() || ''
    }
  } catch {
    // Fall through to simple parsing
  }

  if (imageValue.includes('/')) {
    return imageValue.split('/').pop() || imageValue
  }

  return imageValue
}

const fetchPets = async (role, isInitialLoad = false) => {
  try {
    const token = localStorage.getItem('token')
    const isShelter = role === 'shelter'
    const response = await fetch(
      isShelter ? 'http://localhost:8000/api/shelter/dashboard' : 'http://localhost:8000/api/pets',
      {
        headers: isShelter
          ? {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json'
            }
          : undefined
      }
    )
    const data = await response.json()
    const petList = isShelter ? data.pets : data

    pets.value = petList.map(pet => {
      const imageName = normalizeImageName(pet.image)
      return {
        ...pet,
        image: imageName,
        imageUrl: getImagePath(imageName) || getImagePath(pet.image)
      }
    })
  } catch (error) {
    console.error('Failed to fetch pets:', error)
    pets.value = []
  } finally {
    if (isInitialLoad) {
      loading.value = false
    }
  }
}

onMounted(async () => {
  try {
    loading.value = true
    const token = localStorage.getItem('token')
    
    // Fetch fresh user data from API - always get current user
    const response = await fetch('http://localhost:8000/api/user', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      const freshUser = await response.json()
      user.value = freshUser
      localStorage.setItem('user', JSON.stringify(freshUser))
      
      // Now fetch pets with the correct role (with isInitialLoad = true to manage loading state)
      await fetchPets(freshUser.role, true)
    } else {
      loading.value = false
    }
  } catch (error) {
    console.error('Failed to fetch user:', error)
    loading.value = false
  }
})

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/')
}

const handleRefreshPets = () => {
  if (user.value?.role) {
    fetchPets(user.value.role, false)
  }
}

</script>

<template>
  <div class="dashboard">
    <DashboardHeader :user="user" />
    
    <div v-if="loading" class="loading-container">
      <p>Loading dashboard...</p>
    </div>
    <div v-else>
      <UserDashboard v-if="!user?.role || user?.role === 'user'" :pets="pets" />
      <ShelterDashboard v-else-if="user?.role === 'shelter'" :pets="pets" @refresh-pets="handleRefreshPets" />
      <AdminDashboard v-else-if="user?.role === 'admin'" :pets="pets" />
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f9fafb;
}

.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  color: #6b7280;
  font-size: 16px;
}
</style>
