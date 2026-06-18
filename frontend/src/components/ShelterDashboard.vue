<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
  pets: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['refresh-pets'])

const applications = ref([])
const completedAdoptions = ref(0)
const adoptedPets = ref([])
const selectedApplication = ref(null)
const showApplicationModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const viewingPets = ref(false)
const viewingApplications = ref(false)
const viewingAdopted = ref(false)

const handlePetsClick = () => {
  viewingPets.value = true
  viewingApplications.value = false
  viewingAdopted.value = false
}
const handleApplicationsClick = () => {
  viewingPets.value = false
  viewingApplications.value = true
  viewingAdopted.value = false
}
const handleAdoptedClick = () => {
  viewingPets.value = false
  viewingApplications.value = false
  viewingAdopted.value = true
}

const handleDogsClick = () => {
  viewingPets.value = true
  viewingApplications.value = false
  viewingAdopted.value = false
  petStatusFilter.value = ''
  speciesFilter.value = 'dog'
}

const speciesFilter = ref('')
const selectedPet = ref(null)
const petToDelete = ref(null)

const editForm = ref({
  name: '',
  species: 'dog',
  breed: '',
  age: '',
  gender: '',
  location: '',
  image: '',
  status: 'available'
})

const petStatusFilter = ref('available')
const editImagePreview = ref(null)
const editUploading = ref(false)

const handleEditImageSelect = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => { editImagePreview.value = e.target.result }
  reader.readAsDataURL(file)

  editUploading.value = true
  try {
    const formData = new FormData()
    formData.append('image', file)
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/upload`, {
      method: 'POST',
      headers: token ? { 'Authorization': `Bearer ${token}` } : {},
      body: formData
    })
    const data = await response.json()
    if (response.ok && data.url) {
      editForm.value.image = data.url
      editImagePreview.value = data.url
    }
  } catch (err) {
    console.error('Upload error:', err)
  } finally {
    editUploading.value = false
  }
}

const pendingApplications = computed(() =>
  applications.value.filter(app => app.status === 'pending')
)

const totalDogs = computed(() =>
  props.pets.filter(pet => pet.species === 'dog')
)


const filteredPets = computed(() => {
  let result = props.pets
  if (petStatusFilter.value) {
    result = result.filter(pet => pet.status === petStatusFilter.value)
  }
  if (speciesFilter.value) {
    result = result.filter(pet => pet.species === speciesFilter.value)
  }
  return result
})

const activePets = computed(() =>
  props.pets.filter(pet => !adoptedPetIds.value.has(pet.id) && pet.status !== 'adopted')
)

const adoptedPetIds = computed(() => {
  return new Set(adoptedPets.value.map(app => app.pet_id))
})

const visibleApplications = computed(() =>
  applications.value.filter(app => app.status !== 'approved' && app.pet_status !== 'adopted')
)

const fetchApplications = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/shelter/dashboard`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      const data = await response.json()
      applications.value = data.applications || []
      completedAdoptions.value = data.completed_adoptions || 0
      adoptedPets.value = data.adopted_pets || []
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
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/shelter/applications/${applicationId}/status`, {
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

const openEditModal = (pet) => {
  selectedPet.value = pet
  editForm.value = {
    name: pet.name,
    species: pet.species || 'dog',
    breed: pet.breed,
    age: pet.age,
    gender: pet.gender || '',
    location: pet.location || '',
    image: pet.image || '',
    status: pet.status
  }
  // If image is a full URL (uploaded file), show it as preview
  if (pet.image && pet.image.startsWith('http')) {
    editImagePreview.value = pet.image
  } else {
    editImagePreview.value = null
  }
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  selectedPet.value = null
  editImagePreview.value = null
}

const updatePet = async () => {
  try {
    const token = localStorage.getItem('token')
    
    // Build pet data - always include all fields including the original image
    const petData = {
      name: editForm.value.name,
      species: editForm.value.species,
      breed: editForm.value.breed,
      age: editForm.value.age,
      gender: editForm.value.gender,
      location: editForm.value.location,
      status: editForm.value.status,
      image: editForm.value.image
    }
    
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/pets/${selectedPet.value.id}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(petData)
    })

    if (response.ok) {
      emit('refresh-pets')
      closeEditModal()
    } else {
      const error = await response.json()
      console.error('Update error:', error)
      alert(`Failed to update pet: ${error.message || JSON.stringify(error)}`)
    }
  } catch (error) {
    console.error('Failed to update pet:', error)
    alert('Failed to update pet: ' + error.message)
  }
}

const openDeleteModal = (pet) => {
  petToDelete.value = pet
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  petToDelete.value = null
}

const confirmDelete = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/pets/${petToDelete.value.id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      emit('refresh-pets')
      closeDeleteModal()
    } else {
      alert('Failed to delete pet')
    }
  } catch (error) {
    console.error('Failed to delete pet:', error)
    alert('Failed to delete pet')
  }
}

onMounted(() => {
  fetchApplications()
})

const router = useRouter()

const goAddPet = () => {
  router.push({ name: 'AddPet' })
}

const getPetImage = (imageName) => {
  if (!imageName) return ''
  if (imageName.startsWith('http://') || imageName.startsWith('https://') || imageName.startsWith('data:') || imageName.startsWith('/')) {
    return imageName
  }
  try {
    return new URL(`../assets/images/${imageName}`, import.meta.url).href
  } catch {
    return ''
  }
}
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
        <button class="primary-btn" @click="goAddPet">+ Add New Pet</button>
      </div>

      <div class="stats-grid">
        <div class="stat-card clickable-card" @click="handlePetsClick">
          <div class="stat-info">
            <h3>Your Pets</h3>
            <p class="stat-number">{{ props.pets.length }}</p>
          </div>
        </div>
        <div class="stat-card clickable-card" @click="handleApplicationsClick">
          <div class="stat-info">
            <h3>Pending Requests</h3>
            <p class="stat-number">{{ pendingApplications.length }}</p>
          </div>
        </div>
        <div class="stat-card clickable-card" @click="handleAdoptedClick">
          <div class="stat-info">
            <h3>Completed Adoptions</h3>
            <p class="stat-number">{{ completedAdoptions }}</p>
          </div>
        </div>
        <div class="stat-card clickable-card" @click="handleDogsClick">
          <div class="stat-info">
            <h3>Total Dogs</h3>
            <p class="stat-number">{{ totalDogs.length }}</p>
          </div>
        </div>
      </div>

      <!-- Your Pets -->
      <div v-if="viewingPets" class="shelter-section">
        <h2>Your Pets</h2>
        <div class="filter-row">
          <button class="filter-btn" :class="{ active: petStatusFilter === 'available' }" @click="petStatusFilter = 'available'; speciesFilter = ''">Available</button>
          <button class="filter-btn" :class="{ active: petStatusFilter === 'pending' }" @click="petStatusFilter = 'pending'; speciesFilter = ''">Pending</button>
          <button class="filter-btn" :class="{ active: petStatusFilter === 'adopted' }" @click="petStatusFilter = 'adopted'; speciesFilter = ''">Adopted</button>
          <button class="filter-btn" :class="{ active: petStatusFilter === '' }" @click="petStatusFilter = ''; speciesFilter = ''">All</button>
        </div>
        <div class="pets-grid-small">
          <div v-for="pet in filteredPets" :key="pet.id" class="pet-card-small">
            <img :src="pet.imageUrl || getPetImage(pet.image)" :alt="pet.name" />
            <div class="pet-card-info">
              <h4>{{ pet.name }}</h4>
              <p>{{ pet.breed }}</p>
              <div class="pet-actions">
                <button class="btn-edit" @click="openEditModal(pet)">Edit</button>
                <button class="btn-delete" @click="openDeleteModal(pet)">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Applications -->
      <div v-if="viewingApplications" class="shelter-section">
        <h2>Pending Requests</h2>
        <div v-if="visibleApplications.length === 0" class="empty-state">No applications yet</div>
        <div v-else class="applications-list">
          <div v-for="app in visibleApplications" :key="app.id" class="application-item">
            <img :src="getPetImage(app.pet_image)" :alt="app.pet_name" class="app-pet-image" />
            <div class="app-info">
              <h3>{{ app.full_name }}</h3>
              <p>Applied for <strong>{{ app.pet_name }}</strong></p>
              <p class="app-date">{{ app.applied_at }}</p>
            </div>
            <div class="app-status">
              <span class="status-badge" :class="`status-${app.status}`">{{ app.status }}</span>
            </div>
            <button class="view-btn" @click="viewApplication(app)">View Details</button>
          </div>
        </div>
      </div>

      <!-- Adopted Pets -->
      <div v-if="viewingAdopted" class="shelter-section">
        <h2>Completed Adoptions</h2>
        <div v-if="adoptedPets.length === 0" class="empty-state">No adoptions yet</div>
        <div v-else class="applications-list">
          <div v-for="app in adoptedPets" :key="app.application_id" class="application-item">
            <img :src="getPetImage(app.pet_image)" :alt="app.pet_name" class="app-pet-image" />
            <div class="app-info">
              <h3>{{ app.pet_name }}</h3>
              <p>Adopted by <strong>{{ app.adopted_by }}</strong></p>
              <p class="app-date">Adopted on {{ app.adopted_at }}</p>
            </div>
            <div class="app-status">
              <span class="status-badge status-approved">approved</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Application Details Modal -->
    <div v-if="showApplicationModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Application Details</h2>
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

    <!-- Edit Pet Modal -->
    <div v-if="showEditModal" class="modal-overlay" @click="closeEditModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Edit Pet</h2>
        </div>

        <div class="modal-body">
          <form @submit.prevent="updatePet" class="edit-form">
            <div class="form-group">
              <label>Name</label>
              <input type="text" v-model="editForm.name" required class="form-input">
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Species</label>
                <select v-model="editForm.species" required class="form-input">
                  <option value="dog">Dog</option>
                  <option value="cat">Cat</option>
                  <option value="rabbit">Rabbit</option>
                  <option value="bird">Bird</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div class="form-group">
                <label>Breed</label>
                <input type="text" v-model="editForm.breed" required class="form-input">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Age</label>
                <input type="text" v-model="editForm.age" required class="form-input" placeholder="e.g., 2 years">
              </div>

              <div class="form-group">
                <label>Gender</label>
                <select v-model="editForm.gender" required class="form-input">
                  <option value="">Select gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Status</label>
                <select v-model="editForm.status" class="form-input">
                  <option value="available">Available</option>
                  <option value="pending">Pending</option>
                  <option value="adopted">Adopted</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>Location</label>
              <input type="text" v-model="editForm.location" required class="form-input" placeholder="e.g., Zagreb">
            </div>

            <div class="form-group">
              <label>Photo</label>
              <div class="upload-area" @click="$refs.editFileInput.click()">
                <div v-if="editUploading" class="upload-placeholder">
                  <div class="spinner"></div>
                  <p>Uploading...</p>
                </div>
                <div v-else-if="editImagePreview" class="preview-container">
                  <img :src="editImagePreview" alt="Preview" class="image-preview" />
                  <p class="change-hint">Click to change image</p>
                </div>
                <div v-else-if="editForm.image && !editForm.image.startsWith('http')" class="preview-container">
                  <img :src="getPetImage(editForm.image)" alt="Preview" class="image-preview" />
                  <p class="change-hint">Click to change image</p>
                </div>
                <div v-else class="upload-placeholder">
                  <p class="upload-text">Click to upload a photo</p>
                </div>
              </div>
              <input ref="editFileInput" type="file" accept="image/*" class="hidden-input" @change="handleEditImageSelect" />
            </div>

            <div class="modal-actions">
              <button type="button" class="btn btn-secondary" @click="closeEditModal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
      <div class="modal-content modal-small" @click.stop>
        <div class="modal-header">
          <h2>Confirm Delete</h2>
        </div>

        <div class="modal-body">
          <p class="delete-message">
            Are you sure you want to delete <strong>{{ petToDelete?.name }}</strong>? 
            This action cannot be undone.
          </p>

          <div class="modal-actions">
            <button type="button" class="btn btn-secondary" @click="closeDeleteModal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="confirmDelete">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hero-shelter {
  background: #10b981;
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
}

.clickable-card {
  cursor: pointer;
  border: 2px solid transparent;
}

.clickable-card:hover {
  border-color: #10b981;
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
}

.shelter-section h2 {
  margin: 0 0 16px;
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.filter-row {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.filter-btn {
  padding: 6px 12px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s;
}


.filter-btn.active {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
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


.btn-danger {
  background: #ef4444;
  color: white;
}


.edit-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
}

.helper-text {
  font-size: 0.75rem;
  color: #6b7280;
  margin: -0.25rem 0 0 0;
}

.form-input {
  padding: 0.625rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
}

textarea.form-input {
  resize: vertical;
  font-family: inherit;
}

.hidden-input { display: none; }

.upload-area {
  border: 2px dashed #d1d5db;
  border-radius: 10px;
  padding: 1.25rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  background: #fafafa;
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}


.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}


.upload-text {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin: 0;
}

.preview-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.image-preview {
  max-height: 150px;
  max-width: 100%;
  border-radius: 6px;
  object-fit: cover;
}

.change-hint {
  font-size: 11px;
  color: #6b7280;
  margin: 0;
}

.spinner {
  width: 28px;
  height: 28px;
  border: 3px solid #e5e7eb;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.btn-primary {
  background: #3b82f6;
  color: white;
}


.btn-secondary {
  background: #6b7280;
  color: white;
}


.modal-small {
  max-width: 400px;
}

.delete-message {
  font-size: 0.9375rem;
  color: #374151;
  line-height: 1.6;
  margin: 0 0 1.5rem 0;
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

  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
