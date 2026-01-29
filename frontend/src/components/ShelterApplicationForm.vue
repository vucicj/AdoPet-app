<script setup>
import { ref } from 'vue'

const form = ref({
  shelterName: '',
  email: '',
  password: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  registrationNumber: '',
  website: ''
})

const loading = ref(false)
const success = ref('')
const error = ref('')

const handleSubmit = async () => {
  error.value = ''
  success.value = ''

  if (!form.value.shelterName || !form.value.email || !form.value.password || !form.value.phone || !form.value.address || !form.value.city || !form.value.state || !form.value.registrationNumber) {
    error.value = 'Please fill in all required fields.'
    return
  }

  loading.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 600))
    success.value = 'Shelter application submitted!'
  } catch (err) {
    error.value = 'Failed to submit application.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="shelter-application-page">
    <div class="form-card">
      <div class="form-header">
        <span class="form-icon">🏠</span>
        <h1>I'm a Shelter</h1>
      </div>

      <p class="form-subtitle">Register your shelter and start helping pets find loving homes.</p>

      <div v-if="success" class="success-message">{{ success }}</div>
      <div v-if="error" class="error-message">{{ error }}</div>

      <form class="form-body" @submit.prevent="handleSubmit">
        <label class="field">
          <span>Shelter Name</span>
          <input v-model="form.shelterName" type="text" placeholder="Happy Paws Shelter" />
        </label>

        <label class="field">
          <span>Email Address</span>
          <input v-model="form.email" type="email" placeholder="contact@shelter.com" />
        </label>

        <label class="field">
          <span>Password</span>
          <input v-model="form.password" type="password" placeholder="••••••••" />
        </label>

        <label class="field">
          <span>Phone Number</span>
          <input v-model="form.phone" type="tel" placeholder="+1 (555) 000-0000" />
        </label>

        <label class="field">
          <span>Address</span>
          <input v-model="form.address" type="text" placeholder="123 Main Street" />
        </label>

        <div class="row">
          <label class="field">
            <span>City</span>
            <input v-model="form.city" type="text" placeholder="New York" />
          </label>
          <label class="field">
            <span>State</span>
            <input v-model="form.state" type="text" placeholder="NY" />
          </label>
        </div>

        <label class="field">
          <span>Registration Number</span>
          <input v-model="form.registrationNumber" type="text" placeholder="SH-12345" />
        </label>

        <label class="field">
          <span>Website (Optional)</span>
          <input v-model="form.website" type="url" placeholder="www.shelter.com" />
        </label>

        <button class="submit-btn" type="submit" :disabled="loading">
          {{ loading ? 'Submitting...' : 'Register Shelter' }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.shelter-application-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: #f5f7fb;
}

.form-card {
  width: 100%;
  max-width: 520px;
  background: #ffffff;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
}

.form-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
}

.form-icon {
  font-size: 1.5rem;
  background: #e8f7f1;
  color: #10b981;
  border-radius: 12px;
  padding: 0.5rem 0.65rem;
}

.form-header h1 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #0f172a;
}

.form-subtitle {
  margin: 0 0 1.5rem 0;
  color: #64748b;
  font-size: 0.95rem;
}

.success-message,
.error-message {
  padding: 0.75rem 1rem;
  border-radius: 10px;
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.success-message {
  background: #ecfdf5;
  color: #047857;
}

.error-message {
  background: #fef2f2;
  color: #b91c1c;
}

.form-body {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  font-size: 0.9rem;
  color: #475569;
  font-weight: 600;
}

.field input {
  padding: 0.85rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.95rem;
  color: #0f172a;
  background: #f8fafc;
}

.field input:focus {
  outline: none;
  border-color: #10b981;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}

.row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.submit-btn {
  margin-top: 0.5rem;
  padding: 0.9rem 1rem;
  border: none;
  border-radius: 12px;
  background: #8fdcc2;
  color: #ffffff;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.submit-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 18px rgba(16, 185, 129, 0.25);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}

@media (max-width: 640px) {
  .row {
    grid-template-columns: 1fr;
  }

  .form-card {
    padding: 1.5rem;
  }
}
</style>
