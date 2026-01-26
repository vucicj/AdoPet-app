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

const handleLogout = () => {
  emit('logout')
}
</script>

<template>
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

@media (max-width: 768px) {
  .nav-links {
    display: none;
  }
}
</style>
