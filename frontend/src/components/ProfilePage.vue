<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref({
  name: '',
  email: '',
  avatar: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 200 200%22%3E%3Crect width=%22200%22 height=%22200%22 fill=%22%23d1d5db%22/%3E%3Ccircle cx=%22100%22 cy=%2255%22 r=%2235%22 fill=%22%23000000%22/%3E%3Cpath d=%22M 30 110 Q 30 85 100 85 Q 170 85 170 110 L 170 175 L 30 175 Z%22 fill=%22%23000000%22/%3E%3C/svg%3E'
})

const applications = ref([])
const loading = ref(true)
const error = ref('')

const showContactModal = ref(false)
const contactInfo = ref({
  petName: '',
  phone: '(555) 123-4567'
})

const pendingApplications = computed(() => {
  return applications.value.filter(app => app.status === 'pending').length
})

const adoptedPets = computed(() => {
  return applications.value.filter(app => app.status === 'approved').length
})

const isUserAShelter = computed(() => {
  const userData = localStorage.getItem('user')
  return userData ? JSON.parse(userData).role === 'shelter' : false
})

const getImagePath = (imageName) => {
  try {
    return new URL(`../assets/images/${imageName}`, import.meta.url).href
  } catch {
    return null
  }
}

const getStatusClass = (status) => {
  return `status-${status}`
}

const getStatusText = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

const editProfile = () => {
  router.push('/profile/edit')
}

const viewSettings = () => {
  router.push('/settings')
}

const closePage = () => {
  router.push('/dashboard')
}

const withdrawApplication = async (id) => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`http://localhost:8000/api/applications/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      applications.value = applications.value.filter(app => app.id !== id)
    } else {
      error.value = 'Failed to withdraw application'
    }
  } catch (err) {
    console.error('Error withdrawing application:', err)
    error.value = 'Error withdrawing application'
  }
}

const contactShelter = (petName) => {
  const shelterPhone = '(555) 123-4567'
  alert(`Shelter Contact Number: ${shelterPhone}\n\nPlease call this number to discuss the adoption of ${petName}`)
}

const showContactInfo = (petName) => {
  contactInfo.value.petName = petName
  showContactModal.value = true
}

const closeContactModal = () => {
  showContactModal.value = false
}

onMounted(async () => {
  try {
    const userData = localStorage.getItem('user')
    if (userData) {
      const parsedUser = JSON.parse(userData)
      user.value.name = parsedUser.name
      user.value.email = parsedUser.email
    }

    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/')
      return
    }

    const userRole = userData ? JSON.parse(userData).role : 'user'

    // Only fetch applications for non-shelter users
    if (userRole !== 'shelter') {
      const response = await fetch('http://localhost:8000/api/applications', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        applications.value = data.map(app => ({
          ...app,
          image: getImagePath(app.image) 
        }))
      } else if (response.status === 401) {
        router.push('/')
      } else {
        error.value = 'Failed to load applications'
      }
    }
  } catch (err) {
    console.error('Error loading profile:', err)
    error.value = 'Error loading profile data'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="profile-page">
    <!-- Error Message -->
    <div v-if="error" class="error-banner">
      {{ error }}
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <p>Loading profile...</p>
    </div>

    <!-- Profile Content -->
    <div v-else>
      <!-- Header Section -->
      <section class="profile-header">
        <button class="close-btn" @click="closePage">✕</button>
        <div class="profile-card">
          <div class="profile-avatar">
            <img :src="user.avatar" :alt="user.name" />
          </div>
          <div class="profile-info">
            <h1 class="profile-name">{{ user.name }}</h1>
            <p class="profile-email">{{ user.email }}</p>
            <div class="profile-actions">
              <button class="btn btn-primary" @click="editProfile">Edit Profile</button>
              <button class="btn btn-secondary" @click="viewSettings">Settings</button>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
          <div class="stat-item">
            <div class="stat-icon applications-icon">📋</div>
            <div class="stat-content">
              <div class="stat-value">{{ pendingApplications }}</div>
              <div class="stat-label">Applications</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon adopted-icon">🏠</div>
            <div class="stat-content">
              <div class="stat-value">{{ adoptedPets }}</div>
              <div class="stat-label">Adopted</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Applications Section - only for non-shelter users -->
      <section class="applications-section" v-if="!isUserAShelter">
        <h2 class="section-title">My Applications</h2>
        
        <div v-if="applications.length === 0" class="empty-state">
          <p>No applications yet. Start exploring pets to apply!</p>
        </div>

        <div v-else class="applications-grid">
          <div v-for="app in applications" :key="app.id" class="application-card">
            <div class="app-image">
              <img :src="app.image" :alt="app.petName" />
            </div>
            <div class="app-content">
              <h3 class="pet-name">{{ app.petName }}</h3>
              <p class="pet-details">{{ app.petType }} • {{ app.age }}</p>
              <p class="applied-date">Applied on {{ app.appliedOn }}</p>
              
              <div class="status-badge" :class="getStatusClass(app.status)">
                {{ getStatusText(app.status) }}
              </div>

              <div class="app-actions">
                <button 
                  class="btn btn-success" 
                  @click="showContactInfo(app.petName)"
                  v-if="app.status === 'approved'"
                >
                  Contact Shelter
                </button>
                <button 
                  class="btn btn-danger" 
                  @click="withdrawApplication(app.id)"
                  v-if="app.status !== 'approved'"
                >
                  Withdraw
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Contact Shelter Modal -->
      <div v-if="showContactModal" class="modal-overlay" @click="closeContactModal">
        <div class="modal-content" @click.stop>
          <button class="modal-close" @click="closeContactModal">✕</button>
          <div class="modal-body">
            <div class="modal-icon">📞</div>
            <h2 class="modal-title">Contact the Shelter</h2>
            <p class="modal-subtitle">For adoption of <strong>{{ contactInfo.petName }}</strong></p>
            
            <div class="contact-section">
              <div class="contact-item">
                <div class="contact-label">Phone</div>
                <a :href="`tel:${contactInfo.phone}`" class="contact-value phone-link">{{ contactInfo.phone }}</a>
              </div>
            </div>

            <div class="modal-actions">
              <button class="btn btn-primary" @click="closeContactModal" style="width: 100%;">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 2rem;
}

.error-banner {
  background: #fee2e2;
  border: 1px solid #fecaca;
  color: #991b1b;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  text-align: center;
  font-weight: 500;
}

.loading-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 500px;
  background: white;
  border-radius: 12px;
  font-size: 1.1rem;
  color: #6b7280;
}

/* Header Section */
.profile-header {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: relative;
}

.close-btn {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  background: none;
  border: none;
  color: #6b7280;
  font-size: 1.75rem;
  cursor: pointer;
  transition: color 0.2s;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  z-index: 10;
}

.close-btn:hover {
  color: #1f2937;
  background: #f3f4f6;
}

.profile-card {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e0e0e0;
}

.profile-avatar {
  flex-shrink: 0;
}

.profile-avatar img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  border: 4px solid #7c3aed;
  object-fit: cover;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.profile-email {
  color: #6b7280;
  font-size: 1rem;
  margin: 0 0 1.5rem 0;
}

.profile-actions {
  display: flex;
  gap: 1rem;
}

.btn {
  padding: 0.625rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.btn-secondary {
  background: #f3f4f6;
  color: #1f2937;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.btn-success {
  background: #10b981;
  color: white;
}

.btn-success:hover {
  background: #059669;
}

.btn-danger {
  background: #ef4444;
  color: white;
  font-size: 0.85rem;
  padding: 0.5rem 1rem;
}

.btn-danger:hover {
  background: #dc2626;
}

/* Quick Stats */
.quick-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1.5rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.saved-icon {
  background: #fce7f3;
}

.applications-icon {
  background: #dbeafe;
}

.adopted-icon {
  background: #dcfce7;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

/* Applications Section */
.applications-section {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1.5rem 0;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.05rem;
}

.applications-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.application-card {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.application-card:hover {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
}

.app-image {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #f3f4f6;
}

.app-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.app-content {
  padding: 1.5rem;
}

.pet-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.pet-details {
  color: #6b7280;
  font-size: 0.95rem;
  margin: 0 0 0.5rem 0;
}

.applied-date {
  color: #9ca3af;
  font-size: 0.85rem;
  margin: 0 0 1rem 0;
}

.status-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 1rem;
  text-transform: capitalize;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-approved {
  background: #d1fae5;
  color: #065f46;
}

.status-rejected {
  background: #fee2e2;
  color: #7f1d1d;
}

.app-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.app-actions .btn {
  flex: 1;
  min-width: 120px;
  padding: 0.625rem 1rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 90%;
  position: relative;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-close {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6b7280;
  cursor: pointer;
  transition: color 0.2s;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.modal-close:hover {
  color: #1f2937;
}

.modal-body {
  padding: 3rem 2rem 2rem;
  text-align: center;
}

.modal-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.modal-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.modal-subtitle {
  color: #6b7280;
  font-size: 1rem;
  margin: 0 0 2rem 0;
}

.contact-section {
  background: #f9fafb;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.contact-item {
  margin-bottom: 1.5rem;
  text-align: left;
}

.contact-item:last-child {
  margin-bottom: 0;
}

.contact-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

.contact-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  text-decoration: none;
  transition: color 0.2s;
  display: inline-block;
}

.phone-link:hover,
.email-link:hover {
  color: #7c3aed;
}

.modal-actions {
  display: flex;
  gap: 1rem;
}

.modal-actions .btn {
  flex: 1;
  padding: 0.75rem 1.5rem;
  font-size: 0.95rem;
}

@media (max-width: 768px) {
  .profile-page {
    padding: 1rem;
  }

  .profile-card {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .profile-actions {
    flex-direction: column;
    width: 100%;
  }

  .profile-actions .btn {
    width: 100%;
  }

  .quick-stats {
    grid-template-columns: 1fr;
  }

  .applications-grid {
    grid-template-columns: 1fr;
  }
}
</style>
