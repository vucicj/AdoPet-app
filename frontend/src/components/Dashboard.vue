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
  try {
    return new URL(`../assets/images/${imageName}`, import.meta.url).href
  } catch {
    return null
  }
}

const fetchPets = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/pets')
    const data = await response.json()

    pets.value = data.map(pet => ({
      ...pet,
      image: getImagePath(pet.image)
    }))
  } catch (error) {
    console.error('Failed to fetch pets:', error)
    pets.value = []
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  const userData = localStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }

  await fetchPets()
})

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/')
}

</script>

<template>
  <div class="dashboard">
    <DashboardHeader :user="user" @logout="handleLogout" />
    
    <UserDashboard v-if="!user?.role || user?.role === 'user'" :pets="pets" />
    <ShelterDashboard v-else-if="user?.role === 'shelter'" :pets="pets" @refresh-pets="fetchPets" />
    <AdminDashboard v-else-if="user?.role === 'admin'" :pets="pets" />
  </div>
</template>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f9fafb;
}
</style>
