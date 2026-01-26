<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import DashboardHeader from './DashboardHeader.vue'
import UserDashboard from './UserDashboard.vue'
import ShelterDashboard from './ShelterDashboard.vue'
import AdminDashboard from './AdminDashboard.vue'
import dog1 from '@/assets/images/dog1.jpg'
import dog2 from '@/assets/images/dog2.webp'
import cat1 from '@/assets/images/cat1.jpg'
import cat2 from '@/assets/images/cat2.jpg'
import rabbit1 from '@/assets/images/rabbit1.webp'
import rabbit2 from '@/assets/images/rabbit2.webp'
import bird1 from '@/assets/images/bird1.jpg'
import bird2 from '@/assets/images/bird2.jpg'

const router = useRouter()
const user = ref(null)

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

</script>

<template>
  <div class="dashboard">
    <DashboardHeader :user="user" @logout="handleLogout" />
    
    <UserDashboard v-if="!user?.role || user?.role === 'user'" :pets="pets" />
    <ShelterDashboard v-else-if="user?.role === 'shelter'" :pets="pets" />
    <AdminDashboard v-else-if="user?.role === 'admin'" :pets="pets" />
  </div>
</template>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f9fafb;
}
</style>
