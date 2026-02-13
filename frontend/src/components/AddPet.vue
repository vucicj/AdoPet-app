<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = ref({
  name: '',
  breed: '',
  age: '',
  gender: '',
  location: '',
  distance: '',
  image: '',
  status: 'available'
})

const imageFile = ref(null)
const imagePreview = ref(null)
const errorMessage = ref('')

const handleImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    imageFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
    form.value.image = file.name
  }
}

const submitPet = async () => {
  try {
    const token = localStorage.getItem('token')
    
    const petData = {
      name: form.value.name,
      breed: form.value.breed,
      age: form.value.age,
      gender: form.value.gender,
      location: form.value.location,
      distance: form.value.distance,
      status: form.value.status,
      image: form.value.image
    }
    
    const response = await fetch('http://localhost:8000/api/pets', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(petData)
    })

    if (response.ok) {
      router.push('/dashboard')
    } else {
      const error = await response.json()
      errorMessage.value = error.message || 'Failed to add pet'
    }
  } catch (error) {
    console.error('Failed to add pet:', error)
    errorMessage.value = 'Failed to add pet: ' + error.message
  }
}

const cancel = () => {
  router.push('/dashboard')
}
</script>

<template>
  <div class="add-pet-page">
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">Add New Pet</h1>
        <p class="hero-subtitle">Add a new pet to your shelter</p>
      </div>
    </section>

    <div class="form-container">
      <form @submit.prevent="submitPet" class="add-pet-form">
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <div class="form-group">
          <label>Pet Name *</label>
          <input type="text" v-model="form.name" required class="form-input" placeholder="e.g., Max">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Breed *</label>
            <input type="text" v-model="form.breed" required class="form-input" placeholder="e.g., Golden Retriever">
          </div>

          <div class="form-group">
            <label>Age *</label>
            <input type="text" v-model="form.age" required class="form-input" placeholder="e.g., 2 years">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Gender *</label>
            <select v-model="form.gender" required class="form-input">
              <option value="">Select gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
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

        <div class="form-row">
          <div class="form-group">
            <label>Location *</label>
            <input type="text" v-model="form.location" required class="form-input" placeholder="e.g., Austin">
          </div>

          <div class="form-group">
            <label>Distance *</label>
            <input type="text" v-model="form.distance" required class="form-input" placeholder="e.g., 5 miles">
          </div>
        </div>

        <div class="form-group">
          <label>Upload Picture</label>
          <input 
            type="file" 
            @change="handleImageSelect" 
            accept="image/*" 
            class="form-input file-input"
          >
          <p class="helper-text">Choose an image file for the pet</p>
          <div v-if="imagePreview" class="image-preview-container">
            <img :src="imagePreview" alt="Preview" class="image-preview">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" @click="cancel">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Pet</button>
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

.form-container {
  max-width: 800px;
  margin: -40px auto 40px;
  padding: 0 24px;
}

.add-pet-form {
  background: white;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
  color: #6b7280;
  margin: 0;
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
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.file-input {
  cursor: pointer;
  padding: 8px;
}

.file-input::file-selector-button {
  padding: 8px 16px;
  margin-right: 16px;
  border: none;
  border-radius: 6px;
  background: #10b981;
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.file-input::file-selector-button:hover {
  background: #059669;
}

.image-preview-container {
  margin-top: 16px;
  border: 2px dashed #d1d5db;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
  background: #f9fafb;
}

.image-preview {
  max-width: 100%;
  max-height: 300px;
  border-radius: 6px;
  object-fit: contain;
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

.btn-primary:hover {
  background: #059669;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 32px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .add-pet-form {
    padding: 24px;
  }
}
</style>
