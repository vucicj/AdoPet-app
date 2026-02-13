<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const error = ref('')
const success = ref('')

const passwordForm = ref({
  currentPassword: '',
  password: '',
  passwordConfirmation: ''
})

const goBack = () => {
  router.push('/profile')
}

const resetPasswordForm = () => {
  passwordForm.value = {
    currentPassword: '',
    password: '',
    passwordConfirmation: ''
  }
  error.value = ''
  success.value = ''
}

const validatePasswordForm = () => {
  if (!passwordForm.value.currentPassword) {
    error.value = 'Current password is required'
    return false
  }
  if (!passwordForm.value.password) {
    error.value = 'New password is required'
    return false
  }
  if (passwordForm.value.password.length < 8) {
    error.value = 'Password must be at least 8 characters'
    return false
  }
  if (passwordForm.value.password !== passwordForm.value.passwordConfirmation) {
    error.value = 'Passwords do not match'
    return false
  }
  return true
}

const savePassword = async () => {
  if (!validatePasswordForm()) {
    return
  }

  loading.value = true
  error.value = ''
  success.value = ''

  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://localhost:8000/api/user/password', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        current_password: passwordForm.value.currentPassword,
        password: passwordForm.value.password,
        password_confirmation: passwordForm.value.passwordConfirmation
      })
    })

    if (response.ok) {
      success.value = 'Password changed successfully!'
      resetPasswordForm()
      setTimeout(() => {
        router.push('/profile')
      }, 1500)
    } else {
      const data = await response.json()
      error.value = data.message || 'Failed to change password'
    }
  } catch (err) {
    console.error('Error changing password:', err)
    error.value = 'Error changing password'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="settings-page">
    <div class="container">
      <div class="settings-card">
        <div class="header">
          <h1>Settings</h1>
        </div>

        <h2>Change Password</h2>
        
        <div v-if="error" class="alert alert-error">{{ error }}</div>
        <div v-if="success" class="alert alert-success">{{ success }}</div>

        <form @submit.prevent="savePassword" class="form">
          <div class="form-group">
            <label for="currentPassword">Current Password</label>
            <input 
              id="currentPassword"
              v-model="passwordForm.currentPassword" 
              type="password" 
              placeholder="Enter your current password"
              class="input"
            />
          </div>

          <div class="form-group">
            <label for="password">New Password</label>
            <input 
              id="password"
              v-model="passwordForm.password" 
              type="password" 
              placeholder="Enter new password (min 8 characters)"
              class="input"
            />
          </div>

          <div class="form-group">
            <label for="passwordConfirmation">Confirm New Password</label>
            <input 
              id="passwordConfirmation"
              v-model="passwordForm.passwordConfirmation" 
              type="password" 
              placeholder="Confirm new password"
              class="input"
            />
          </div>

          <div class="form-actions">
            <button 
              type="button" 
              class="btn btn-secondary" 
              @click="resetPasswordForm"
              :disabled="loading"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="btn btn-primary" 
              :disabled="loading"
            >
              {{ loading ? 'Saving...' : 'Change Password' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.settings-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.container {
  width: 100%;
  max-width: 500px;
}

.settings-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
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

.header h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.tab-content h2 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
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

.form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #1f2937;
  font-size: 0.95rem;
}

.input {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  transition: all 0.2s;
}

.input:focus {
  outline: none;
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
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

@media (max-width: 640px) {
  .settings-page {
    padding: 1rem;
  }

  .settings-card {
    padding: 1.5rem;
  }

  .header h1 {
    font-size: 1.25rem;
  }
}
</style>
