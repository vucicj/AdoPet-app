<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const pet = ref(null)
const loading = ref(true)
const error = ref('')
const success = ref('')

const formData = ref({
  fullName: '',
  email: '',
  phoneNumber: '',
  age: '',
  residenceType: '',
  ownOrRent: '',
  hasYard: '',
  ownedPetsBefore: '',
  hasOtherPets: '',
  otherPetsDetails: '',
  hoursAlone: '',
  adoptionReason: '',
  agreement: false
})

const closePage = () => {
  router.push('/dashboard')
}

const resetForm = () => {
  formData.value = {
    fullName: '',
    email: '',
    phoneNumber: '',
    age: '',
    residenceType: '',
    ownOrRent: '',
    hasYard: '',
    ownedPetsBefore: '',
    hasOtherPets: '',
    otherPetsDetails: '',
    hoursAlone: '',
    adoptionReason: '',
    agreement: false
  }
}

const validateForm = () => {
  if (!formData.value.fullName || !formData.value.email || !formData.value.phoneNumber) {
    error.value = 'Please fill in all required personal information fields'
    return false
  }
  if (!formData.value.residenceType || !formData.value.ownOrRent || !formData.value.hasYard) {
    error.value = 'Please complete the living situation section'
    return false
  }
  if (!formData.value.ownedPetsBefore || !formData.value.hasOtherPets) {
    error.value = 'Please complete the pet experience section'
    return false
  }
  if (!formData.value.hoursAlone || !formData.value.adoptionReason) {
    error.value = 'Please complete the commitment & care section'
    return false
  }
  if (!formData.value.agreement) {
    error.value = 'You must agree to the terms before submitting'
    return false
  }
  return true
}

const submitApplication = async () => {
  error.value = ''
  success.value = ''

  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://localhost:8000/api/applications', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        pet_id: route.params.petId,
        full_name: formData.value.fullName,
        email: formData.value.email,
        phone_number: formData.value.phoneNumber,
        age: formData.value.age,
        residence_type: formData.value.residenceType,
        own_or_rent: formData.value.ownOrRent,
        has_yard: formData.value.hasYard,
        owned_pets_before: formData.value.ownedPetsBefore,
        has_other_pets: formData.value.hasOtherPets,
        other_pets_details: formData.value.otherPetsDetails,
        hours_alone: formData.value.hoursAlone,
        adoption_reason: formData.value.adoptionReason
      })
    })

    if (response.ok) {
      success.value = 'Application submitted successfully!'
      setTimeout(() => {
        router.push('/profile')
      }, 2000)
    } else {
      const data = await response.json()
      error.value = data.message || 'Failed to submit application'
    }
  } catch (err) {
    console.error('Error submitting application:', err)
    error.value = 'Error submitting application'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  const petId = route.params.petId
  if (!petId) {
    router.push('/dashboard')
    return
  }

  try {
    const response = await fetch(`http://localhost:8000/api/pets/${petId}`)
    if (response.ok) {
      pet.value = await response.json()
    } else {
      error.value = 'Pet not found'
    }

    // Pre-fill user data
    const userData = localStorage.getItem('user')
    if (userData) {
      const user = JSON.parse(userData)
      formData.value.fullName = user.name
      formData.value.email = user.email
    }
  } catch (err) {
    console.error('Error loading pet:', err)
    error.value = 'Error loading pet information'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="application-page">
    <div class="container">
      <div class="application-card">
        <!-- Header -->
        <div class="header">
          <h1>Adoption Application</h1>
          <button class="close-btn" @click="closePage">✕</button>
        </div>
        <p class="subtitle" v-if="pet">Help us find the perfect match for {{ pet.name }}</p>

        <!-- Alerts -->
        <div v-if="error" class="alert alert-error">{{ error }}</div>
        <div v-if="success" class="alert alert-success">{{ success }}</div>

        <!-- Before You Apply -->
        <div class="info-section">
          <div class="info-icon">ℹ️</div>
          <div class="info-content">
            <h3>Before you apply</h3>
            <p>Please be sure you have discussed this decision with all family members and only apply for this pet/breed if you are willing to wait and ready to commit. Our adoption will be willing to answer any question you may have about each of our adoptable dogs.</p>
          </div>
        </div>

        <!-- Pet Info -->
        <div v-if="pet" class="pet-info">
          <img :src="pet.image" :alt="pet.name" class="pet-avatar" />
          <div class="pet-details">
            <h3>{{ pet.name }}</h3>
            <p>{{ pet.breed }} • {{ pet.age }}</p>
          </div>
        </div>

        <form @submit.prevent="submitApplication">
          <!-- Personal Information -->
          <section class="form-section">
            <h2>Personal Information</h2>
            <div class="form-row">
              <div class="form-group">
                <label>Full Name *</label>
                <input v-model="formData.fullName" type="text" placeholder="John Doe" class="input" />
              </div>
              <div class="form-group">
                <label>Email *</label>
                <input v-model="formData.email" type="email" placeholder="john@example.com" class="input" />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Phone Number *</label>
                <input v-model="formData.phoneNumber" type="tel" placeholder="(555) 123-4567" class="input" />
              </div>
              <div class="form-group">
                <label>Age</label>
                <input v-model="formData.age" type="number" placeholder="25" class="input" />
              </div>
            </div>
          </section>

          <!-- Living Situation -->
          <section class="form-section">
            <h2>Living Situation</h2>
            <div class="form-group">
              <label>Type of Residence *</label>
              <select v-model="formData.residenceType" class="input">
                <option value="">Select...</option>
                <option value="house">House</option>
                <option value="apartment">Apartment</option>
                <option value="condo">Condo</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label>Do you own or rent? *</label>
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" v-model="formData.ownOrRent" value="own" />
                  <span>Own</span>
                </label>
                <label class="radio-label">
                  <input type="radio" v-model="formData.ownOrRent" value="rent" />
                  <span>Rent</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label>Do you have a yard? *</label>
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" v-model="formData.hasYard" value="yes" />
                  <span>Yes</span>
                </label>
                <label class="radio-label">
                  <input type="radio" v-model="formData.hasYard" value="no" />
                  <span>No</span>
                </label>
              </div>
            </div>
          </section>

          <!-- Pet Experience -->
          <section class="form-section">
            <h2>Pet Experience</h2>
            <div class="form-group">
              <label>Have you owned pets before? *</label>
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" v-model="formData.ownedPetsBefore" value="yes" />
                  <span>Yes</span>
                </label>
                <label class="radio-label">
                  <input type="radio" v-model="formData.ownedPetsBefore" value="no" />
                  <span>No</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label>Do you currently have other pets? *</label>
              <div class="radio-group">
                <label class="radio-label">
                  <input type="radio" v-model="formData.hasOtherPets" value="yes" />
                  <span>Yes</span>
                </label>
                <label class="radio-label">
                  <input type="radio" v-model="formData.hasOtherPets" value="no" />
                  <span>No</span>
                </label>
              </div>
            </div>
            <div class="form-group" v-if="formData.hasOtherPets === 'yes'">
              <label>Please describe (like any current pets)</label>
              <textarea v-model="formData.otherPetsDetails" class="textarea" rows="4" placeholder="Describe your current pets..."></textarea>
            </div>
          </section>

          <!-- Commitment & Care -->
          <section class="form-section">
            <h2>Commitment & Care</h2>
            <div class="form-group">
              <label>How many hours per day will the pet be alone? *</label>
              <input v-model="formData.hoursAlone" type="text" placeholder="2-4 hours" class="input" />
            </div>
            <div class="form-group">
              <label>Why do you want to adopt this pet? *</label>
              <textarea v-model="formData.adoptionReason" class="textarea" rows="4" placeholder="Tell us your motivation..."></textarea>
            </div>
          </section>

          <!-- Agreement -->
          <div class="agreement">
            <label class="checkbox-label">
              <input type="checkbox" v-model="formData.agreement" />
              <span>I understand that adopting a pet is a long term commitment and I am prepared to provide the care, time, and resources needed for the lifetime of this animal</span>
            </label>
          </div>

          <!-- Actions -->
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" @click="closePage" :disabled="loading">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Submitting...' : 'Submit Application' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.application-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 2rem;
}

.container {
  max-width: 700px;
  margin: 0 auto;
}

.application-card {
  background: white;
  border-radius: 12px;
  padding: 2.5rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.header h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
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

.subtitle {
  color: #6b7280;
  font-size: 0.95rem;
  margin: 0 0 1.5rem 0;
}

.alert {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  font-weight: 500;
}

.alert-error {
  background: #fee2e2;
  border: 1px solid #fecaca;
  color: #991b1b;
}

.alert-success {
  background: #d1fae5;
  border: 1px solid #a7f3d0;
  color: #065f46;
}

.info-section {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.info-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.info-content h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #1e40af;
}

.info-content p {
  margin: 0;
  font-size: 0.875rem;
  color: #1e40af;
  line-height: 1.5;
}

.pet-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.pet-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #7c3aed;
}

.pet-details h3 {
  margin: 0 0 0.25rem 0;
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
}

.pet-details p {
  margin: 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.form-section {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.form-section:last-of-type {
  border-bottom: none;
}

.form-section h2 {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1.25rem 0;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group:last-child {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.95rem;
  font-family: inherit;
  transition: all 0.2s;
}

.input:focus {
  outline: none;
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.textarea {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.95rem;
  font-family: inherit;
  resize: vertical;
  transition: all 0.2s;
}

.textarea:focus {
  outline: none;
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.radio-group {
  display: flex;
  gap: 1.5rem;
}

.radio-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-weight: 400;
  color: #374151;
}

.radio-label input[type="radio"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.agreement {
  margin: 1.5rem 0;
  padding: 1rem;
  background: #fef3c7;
  border: 1px solid #fde68a;
  border-radius: 8px;
}

.checkbox-label {
  display: flex;
  gap: 0.75rem;
  cursor: pointer;
  font-size: 0.875rem;
  color: #92400e;
  line-height: 1.5;
}

.checkbox-label input[type="checkbox"] {
  flex-shrink: 0;
  width: 18px;
  height: 18px;
  margin-top: 2px;
  cursor: pointer;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  flex: 1;
}

.btn-primary {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f3f4f6;
  color: #1f2937;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover:not(:disabled) {
  background: #e5e7eb;
}

.btn-secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .application-page {
    padding: 1rem;
  }

  .application-card {
    padding: 1.5rem;
  }

  .header h1 {
    font-size: 1.5rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }
}
</style>
