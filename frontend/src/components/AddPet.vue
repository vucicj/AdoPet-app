<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import DashboardHeader from './DashboardHeader.vue'

const router = useRouter()
const headerUser = ref(null)
const loading = ref(false)
const uploading = ref(false)
const errorMessage = ref('')
const imagePreview = ref(null)

onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) headerUser.value = JSON.parse(userData)
})

const form = ref({
  name: '',
  species: 'dog',
  breed: '',
  age: '',
  gender: '',
  location: '',
  image: '',
  status: 'available'
})

const handleImageSelect = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Show local preview immediately
  const reader = new FileReader()
  reader.onload = (e) => { imagePreview.value = e.target.result }
  reader.readAsDataURL(file)

  // Upload to backend
  uploading.value = true
  errorMessage.value = ''
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
      form.value.image = data.url
    } else {
      errorMessage.value = data.error || data.message || 'Failed to upload image'
      imagePreview.value = null
    }
  } catch (err) {
    errorMessage.value = 'Network error - check if backend is running on port 8000'
    imagePreview.value = null
  } finally {
    uploading.value = false
  }
}

const submitPet = async () => {
  if (loading.value) return
  loading.value = true
  errorMessage.value = ''

  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/pets`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        name: form.value.name,
        species: form.value.species,
        breed: form.value.breed,
        age: form.value.age,
        gender: form.value.gender,
        location: form.value.location,
        status: form.value.status,
        image: form.value.image
      })
    })

    if (response.ok) {
      router.push('/dashboard')
    } else {
      const error = await response.json()
      errorMessage.value = error.message || 'Failed to add pet'
    }
  } catch (error) {
    errorMessage.value = 'Failed to add pet: ' + error.message
  } finally {
    loading.value = false
  }
}

const cancel = () => router.push('/dashboard')
</script>

<template>
  <div class="add-pet-page">
    <DashboardHeader :user="headerUser" />

    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">Add New Pet</h1>
        <p class="hero-subtitle">Add a new pet to your shelter</p>
      </div>
    </section>

    <div class="form-container">
      <form @submit.prevent="submitPet" class="add-pet-form">
        <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>

        <div class="form-group">
          <label>Pet Name *</label>
          <input type="text" v-model="form.name" required class="form-input" placeholder="e.g., Max">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Species *</label>
            <select v-model="form.species" required class="form-input">
              <option value="dog">Dog</option>
              <option value="cat">Cat</option>
              <option value="rabbit">Rabbit</option>
              <option value="bird">Bird</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label>Breed *</label>
            <input type="text" v-model="form.breed" required class="form-input" placeholder="e.g., Golden Retriever">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Age *</label>
            <input type="text" v-model="form.age" required class="form-input" placeholder="e.g., 2 years">
          </div>
          <div class="form-group">
            <label>Gender *</label>
            <select v-model="form.gender" required class="form-input">
              <option value="">Select gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Location *</label>
            <input type="text" v-model="form.location" required class="form-input" placeholder="e.g., Zagreb">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select v-model="form.status" class="form-input">
              <option value="available">Available</option>
              <option value="pending">Pending</option>
              <option value="adopted">Adopted</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Photo *</label>
          <div class="upload-area" @click="$refs.fileInput.click()">
            <div v-if="uploading" class="upload-placeholder">
              <div class="spinner"></div>
              <p>Uploading...</p>
            </div>
            <div v-else-if="imagePreview" class="preview-container">
              <img :src="imagePreview" alt="Preview" class="image-preview" />
              <p class="change-hint">Click to change image</p>
            </div>
            <div v-else class="upload-placeholder">
              <div class="upload-icon">📷</div>
              <p class="upload-text">Click to upload a photo</p>
              <p class="upload-sub">JPG, PNG, WEBP up to 5MB</p>
            </div>
          </div>
          <input
            ref="fileInput"
            type="file"
            accept="image/*"
            class="hidden-input"
            @change="handleImageSelect"
          />
          <p v-if="!form.image && !uploading" class="helper-text">Please upload a photo for the pet.</p>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" @click="cancel" :disabled="loading">Cancel</button>
          <button type="submit" class="btn btn-primary" :disabled="loading || uploading || !form.image">
            {{ loading ? 'Adding...' : 'Add Pet' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.add-pet-page {
  min-height: 100vh;
  background: #f9fafb;
}

.hero {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  padding: 60px 24px;
  text-align: center;
}

.hero-content { max-width: 800px; margin: 0 auto; }

.hero-title {
  color: #fff;
  font-size: 42px;
  font-weight: 700;
  margin: 0 0 12px;
}

.hero-subtitle {
  color: rgba(255,255,255,0.95);
  font-size: 18px;
  margin: 0;
}

.form-container {
  max-width: 800px;
  margin: -40px auto 40px;
  padding: 0 24px;
}

.add-pet-form {
  background: white;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.error-message {
  padding: 12px;
  background: #fee2e2;
  color: #991b1b;
  border-radius: 8px;
  font-size: 14px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.helper-text {
  font-size: 12px;
  color: #ef4444;
  margin: 2px 0 0;
}

.form-input {
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
}

.hidden-input {
  display: none;
}

.upload-area {
  border: 2px dashed #d1d5db;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  background: #fafafa;
  min-height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-area:hover {
  border-color: #10b981;
  background: #f0fdf4;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.upload-icon {
  font-size: 2.5rem;
}

.upload-text {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin: 0;
}

.upload-sub {
  font-size: 0.8rem;
  color: #9ca3af;
  margin: 0;
}

.preview-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.image-preview {
  max-height: 200px;
  max-width: 100%;
  border-radius: 8px;
  object-fit: cover;
}

.change-hint {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e5e7eb;
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.form-actions {
  display: flex;
  gap: 16px;
  margin-top: 8px;
}

.btn {
  flex: 1;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #10b981;
  color: white;
}

.btn-primary:hover:not(:disabled) { background: #059669; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover:not(:disabled) { background: #4b5563; }
.btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

@media (max-width: 768px) {
  .hero-title { font-size: 32px; }
  .form-row { grid-template-columns: 1fr; }
  .add-pet-form { padding: 24px; }
}
</style>
