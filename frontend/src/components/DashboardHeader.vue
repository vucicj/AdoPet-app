<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['logout'])

const router = useRouter()

const goToDashboard = () => {
  router.push('/dashboard')
}

const goToProfile = () => {
  router.push('/profile')
}

const handleLogout = async () => {
  try {
    const token = localStorage.getItem('token')
    await fetch('http://localhost:8000/api/logout', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
  } catch {
    // ignore
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/')
  }
}
</script>

<template>
  <header class="header">
    <div class="header-content">
      <div class="logo" @click="goToDashboard" style="cursor: pointer;">
        <span class="paw-icon">🐾</span>
        <span class="brand-name">AdoPet</span>
      </div>

      <div class="user-section">
        <span class="user-name" v-if="user" @click="goToProfile">{{ user.name }}</span>
        <button v-if="user" class="logout-btn" @click="handleLogout">Logout</button>
      </div>
    </div>
  </header>
</template>

<style scoped>
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

.user-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: color 0.2s;
}

.user-name:hover {
  color: #8b5cf6;
}

.logout-btn {
  padding: 6px 14px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.logout-btn:hover {
  background: #dc2626;
}

@media (max-width: 768px) {
  .user-section {
    flex-wrap: wrap;
    justify-content: flex-end;
  }
}
</style>
