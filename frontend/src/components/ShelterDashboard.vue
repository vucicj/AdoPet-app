<script setup>
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
  pets: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['refresh-pets'])

const applications = ref([])
const selectedApplication = ref(null)
const showApplicationModal = ref(false)

const pendingApplications = computed(() => 
  applications.value.filter(app => app.status === 'pending')
)

const approvedApplications = computed(() => 
  applications.value.filter(app => app.status === 'approved')
)

const activePets = computed(() =>
  props.pets.filter(pet => !adoptedPetIds.value.has(pet.id) && pet.status !== 'adopted')
)

const adoptedPetIds = computed(() => {
  return new Set(
    applications.value
      .filter(app => app.status === 'approved')
      .map(app => app.pet_id)
  )
})

const visibleApplications = computed(() =>
  applications.value.filter(app => app.status !== 'approved' && app.pet_status !== 'adopted')
)

const fetchApplications = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://localhost:8000/api/shelter/applications', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      applications.value = await response.json()
    }
  } catch (error) {
    console.error('Failed to fetch applications:', error)
  }
}

const viewApplication = (application) => {
  selectedApplication.value = application
  showApplicationModal.value = true
}

const closeModal = () => {
  showApplicationModal.value = false
  selectedApplication.value = null
}

const updateApplicationStatus = async (applicationId, status) => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`http://localhost:8000/api/shelter/applications/${applicationId}/status`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ status })
    })

    if (response.ok) {
      await fetchApplications()
      emit('refresh-pets')
      closeModal()
    }
  } catch (error) {
    console.error('Failed to update status:', error)
  }
}

onMounted(() => {
  fetchApplications()
})
</script>

<template>
  <div class="shelter-dashboard">
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
            <p class="stat-number">{{ activePets.length }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📋</div>
          <div class="stat-info">
            <h3>Pending Requests</h3>
            <p class="stat-number">{{ pendingApplications.length }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-info">
            <h3>Approved</h3>
            <p class="stat-number">{{ approvedApplications.length }}</p>
          </div>
        </div>
      </div>

      <div class="shelter-sections">
        <!-- Applications Section -->
        <div class="shelter-section">
          <h2>Recent Applications</h2>
          <div v-if="visibleApplications.length === 0" class="empty-state">
            No applications yet
          </div>
          <div v-else class="applications-list">
            <div v-for="app in visibleApplications" :key="app.id" class="application-item">
              <img :src="app.pet_image" :alt="app.pet_name" class="app-pet-image" />
              <div class="app-info">
                <h3>{{ app.full_name }}</h3>
                <p>Applied for <strong>{{ app.pet_name }}</strong></p>
                <p class="app-date">{{ app.applied_at }}</p>
              </div>
              <div class="app-status">
                <span class="status-badge" :class="`status-${app.status}`">
                  {{ app.status }}
                </span>
              </div>
              <button class="view-btn" @click="viewApplication(app)">View Details</button>
            </div>
          </div>
        </div>

        <div class="shelter-section">
          <h2>Your Pets</h2>
          <div class="pets-grid-small">
            <div v-for="pet in activePets" :key="pet.id" class="pet-card-small">
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

    <!-- Application Details Modal -->
    <div v-if="showApplicationModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Application Details</h2>
          <button class="close-btn" @click="closeModal">✕</button>
        </div>

        <div v-if="selectedApplication" class="modal-body">
          <!-- Pet Info -->
          <div class="detail-section">
            <h3>Pet Information</h3>
            <p><strong>Pet:</strong> {{ selectedApplication.pet_name }}</p>
            <p><strong>Applied on:</strong> {{ selectedApplication.applied_at }}</p>
            <p><strong>Status:</strong> <span class="status-badge" :class="`status-${selectedApplication.status}`">{{ selectedApplication.status }}</span></p>
          </div>

          <!-- Personal Information -->
          <div class="detail-section">
            <h3>Personal Information</h3>
            <p><strong>Full Name:</strong> {{ selectedApplication.full_name }}</p>
            <p><strong>Email:</strong> {{ selectedApplication.email }}</p>
            <p><strong>Phone:</strong> {{ selectedApplication.phone_number }}</p>
            <p v-if="selectedApplication.age"><strong>Age:</strong> {{ selectedApplication.age }}</p>
          </div>

          <!-- Living Situation -->
          <div class="detail-section">
            <h3>Living Situation</h3>
            <p><strong>Residence Type:</strong> {{ selectedApplication.residence_type }}</p>
            <p><strong>Own or Rent:</strong> {{ selectedApplication.own_or_rent }}</p>
            <p><strong>Has Yard:</strong> {{ selectedApplication.has_yard }}</p>
          </div>

          <!-- Pet Experience -->
          <div class="detail-section">
            <h3>Pet Experience</h3>
            <p><strong>Owned Pets Before:</strong> {{ selectedApplication.owned_pets_before }}</p>
            <p><strong>Has Other Pets:</strong> {{ selectedApplication.has_other_pets }}</p>
            <p v-if="selectedApplication.other_pets_details"><strong>Details:</strong> {{ selectedApplication.other_pets_details }}</p>
          </div>

          <!-- Commitment & Care -->
          <div class="detail-section">
            <h3>Commitment & Care</h3>
            <p><strong>Hours Alone:</strong> {{ selectedApplication.hours_alone }}</p>
            <p><strong>Adoption Reason:</strong></p>
            <p class="reason-text">{{ selectedApplication.adoption_reason }}</p>
          </div>

          <!-- Actions -->
          <div class="modal-actions" v-if="selectedApplication.status === 'pending'">
            <button class="btn btn-danger" @click="updateApplicationStatus(selectedApplication.id, 'rejected')">
              Reject
            </button>
            <button class="btn btn-success" @click="updateApplicationStatus(selectedApplication.id, 'approved')">
              Approve
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hero-shelter {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

.hero-subtitle {
  color: rgba(255, 255, 255, 0.95);
  font-size: 18px;
  margin: 0;
}

.shelter-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px 24px;
}

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

.shelter-sections {
  display: grid;
  gap: 24px;
}

.shelter-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.shelter-section h2 {
  margin: 0 0 16px;
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
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
  transition: transform 0.2s;
}

.pet-card-small:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

.empty-state {
  padding: 2rem;
  text-align: center;
  color: #9ca3af;
}

.applications-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.application-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  transition: all 0.2s;
}

.application-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.app-pet-image {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
}

.app-info {
  flex: 1;
}

.app-info h3 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
}

.app-info p {
  margin: 0 0 0.25rem 0;
  font-size: 0.875rem;
  color: #6b7280;
}

.app-date {
  font-size: 0.75rem;
  color: #9ca3af;
}

.app-status {
  margin-right: 1rem;
}

.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
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

.view-btn {
  padding: 0.5rem 1rem;
  background: #7c3aed;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.view-btn:hover {
  background: #6d28d9;
}

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
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
}

.close-btn {
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
}

.close-btn:hover {
  color: #1f2937;
  background: #f3f4f6;
}

.modal-body {
  padding: 1.5rem;
}

.detail-section {
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.detail-section:last-of-type {
  border-bottom: none;
}

.detail-section h3 {
  margin: 0 0 1rem 0;
  font-size: 1rem;
  font-weight: 700;
  color: #1f2937;
}

.detail-section p {
  margin: 0 0 0.5rem 0;
  font-size: 0.875rem;
  color: #374151;
}

.detail-section p:last-child {
  margin-bottom: 0;
}

.reason-text {
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 6px;
  line-height: 1.5;
  margin-top: 0.5rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn {
  flex: 1;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
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
}

.btn-danger:hover {
  background: #dc2626;
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 32px;
  }
  
  .application-item {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .view-btn {
    width: 100%;
  }
}
</style>
