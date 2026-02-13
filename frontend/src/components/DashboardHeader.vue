<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
})

const router = useRouter()

const goToProfile = () => {
  router.push('/profile')
}
</script>

<template>
  <header class="header">
    <div class="header-content">
      <div class="logo">
        <span class="paw-icon">🐾</span>
        <span class="brand-name">AdoPet</span>
      </div>
      
      <div class="user-section">
        <span class="user-name" v-if="user" @click="goToProfile" style="cursor: pointer;">{{ user.name }}</span>
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

@media (max-width: 768px) {
  .user-section {
    flex-wrap: wrap;
    justify-content: flex-end;
  }
}
</style>
