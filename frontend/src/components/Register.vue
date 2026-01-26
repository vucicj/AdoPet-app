<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import petImage from '@/assets/images/dog-login.webp'

const router = useRouter()

const form = ref({
  name: '',
  surname: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const goLogin = () => router.push('/')

const handleRegister = async () => {
  try {
    errorMessage.value = ''
    loading.value = true

    const fullName = `${form.value.name} ${form.value.surname}`.trim()

    const response = await fetch('http://localhost:8000/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: fullName,
        email: form.value.email,
        password: form.value.password,
        password_confirmation: form.value.password_confirmation
      })
    })

    const data = await response.json()

    if (!response.ok) {
      if (response.status === 422 && data.errors) {
        const errors = Object.values(data.errors).flat()
        errorMessage.value = errors.join(', ')
      } else {
        errorMessage.value = data.message || 'Registration failed'
      }
      console.error('Registration response:', data)
      return
    }

    successMessage.value = 'Registration successful! Redirecting to login...'
    setTimeout(() => {
      router.push('/')
    }, 2000)
  } catch (error) {
    errorMessage.value = error.message || 'An error occurred'
    console.error('Registration error:', error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <section class="auth-page">
    <div class="auth-card">
      <div class="brand-panel">
        <div class="brand-mark">&#128062;</div>
        <h1 class="brand-title">AdoPet</h1>
        <p class="brand-subtitle">Where every paw finds a forever family</p>
        <div class="brand-illustration">
          <img :src="petImage" alt="Happy dog and cat" />
        </div>
      </div>

      <div class="form-panel">
        <div class="tabs">
          <button class="tab" type="button" @click="goLogin">Login</button>
          <button class="tab active" type="button">Register</button>
        </div>

        <div class="form-content">
          <h2>Create your account</h2>

          <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
          <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>

          <label class="field">
            <span>Name</span>
            <input v-model="form.name" type="text" placeholder="Jane" />
          </label>

          <label class="field">
            <span>Surname</span>
            <input v-model="form.surname" type="text" placeholder="Doe" />
          </label>

          <label class="field">
            <span>Email</span>
            <input v-model="form.email" type="email" placeholder="you@example.com" />
          </label>

          <label class="field">
            <span>Password</span>
            <input v-model="form.password" type="password" placeholder="••••••••" />
          </label>

          <label class="field">
            <span>Confirm Password</span>
            <input v-model="form.password_confirmation" type="password" placeholder="••••••••" />
          </label>

          <button class="primary" type="button" @click="handleRegister" :disabled="loading">
            {{ loading ? 'Creating account...' : 'Create account' }}
          </button>

          <div class="signup-prompt">
            <p>Already have an account? <button class="signup-link" type="button" @click="goLogin">Login</button></p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>

:global(body) {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #8ec5fc, #e0c3fc);
}

.auth-page {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 24px;
}

.auth-card {
  display: grid;
  grid-template-columns: 1fr 1fr;
  width: min(980px, 100%);
  background: #fff;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 20px 50px rgba(72, 52, 126, 0.2);
}

.brand-panel {
  background: linear-gradient(180deg, #b06ab3, #7d5fff);
  color: #fff;
  padding: 48px 42px;
  display: grid;
  justify-items: center;
  gap: 16px;
  text-align: center;
}

.brand-mark {
  font-size: 42px;
}

.brand-title {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
}

.brand-subtitle {
  margin: 0;
  max-width: 240px;
  font-size: 14px;
  opacity: 0.9;
}

.brand-illustration {
  margin-top: 12px;
  background: rgba(255, 255, 255, 0.18);
  border-radius: 12px;
  padding: 14px;
  width: 100%;
  max-width: 260px;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
}

.brand-illustration img {
  width: 100%;
  display: block;
  border-radius: 10px;
}

.form-panel {
  padding: 42px 48px;
  display: grid;
  align-content: start;
  gap: 20px;
  background: #fff;
}

.tabs {
  display: flex;
  justify-content: center;
  gap: 18px;
  font-size: 16px;
}

.tab {
  border: none;
  background: transparent;
  padding: 10px 0;
  color: #8a8f9e;
  font-weight: 600;
  cursor: pointer;
  font-size: 18px;
}

.tab.active {
  color: #6a4fb7;
}

.form-content {
  display: grid;
  gap: 16px;
}

.form-content h2 {
  margin: 0 0 6px;
  font-size: 18px;
  color: #2f2f41;
}

.success-message {
  padding: 12px 14px;
  border-radius: 8px;
  background: #d1fae5;
  color: #065f46;
  font-size: 14px;
  border-left: 4px solid #10b981;
  margin-bottom: 8px;
}

.error-message {
  padding: 12px 14px;
  border-radius: 8px;
  background: #fee;
  color: #c33;
  font-size: 14px;
  border-left: 4px solid #c33;
  margin-bottom: 8px;
}

.field {
  display: grid;
  gap: 8px;
  color: #6a6a7a;
  font-size: 13px;
}

.field input {
  border: 1px solid #e1e3eb;
  border-radius: 8px;
  padding: 12px 14px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.field input:focus {
  border-color: #a48bff;
  box-shadow: 0 0 0 3px rgba(164, 139, 255, 0.18);
}

.options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  color: #7a7f90;
}

.remember {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.remember input {
  width: 16px;
  height: 16px;
}

.link {
  border: none;
  background: transparent;
  color: #9b8fe8;
  font-weight: 600;
  cursor: pointer;
}

.primary {
  width: 100%;
  padding: 12px 16px;
  border: none;
  border-radius: 10px;
  background: #7d5fff;
  color: #fff;
  font-weight: 700;
  cursor: pointer;
  transition: transform 0.12s ease, box-shadow 0.12s ease;
  box-shadow: 0 10px 20px rgba(125, 95, 255, 0.25);
}

.primary:hover {
  transform: translateY(-1px);
}

.primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.signup-prompt {
  text-align: center;
  font-size: 14px;
  color: #7a7f90;
  margin-top: 8px;
}

.signup-prompt p {
  margin: 0;
}

.signup-link {
  border: none;
  background: transparent;
  color: #7d5fff;
  font-weight: 600;
  cursor: pointer;
  text-decoration: underline;
  font-size: 14px;
}

.signup-link:hover {
  color: #6a4fb7;
}

.divider {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 10px;
  color: #9aa0b5;
  font-size: 13px;
}

.divider span {
  height: 1px;
  background: #e3e6ef;
}

.social {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.social-btn {
  border: 1px solid #e1e3eb;
  background: #fff;
  border-radius: 10px;
  padding: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: border-color 0.2s ease, transform 0.12s ease;
}

.social-btn:hover {
  border-color: #c8ccda;
  transform: translateY(-1px);
}

.google {
  color: #e53935;
}

.facebook {
  color: #4267b2;
}

@media (max-width: 900px) {
  .auth-card {
    grid-template-columns: 1fr;
  }

  .brand-panel {
    padding: 36px 30px;
  }

  .form-panel {
    padding: 32px 30px;
  }
}

@media (max-width: 540px) {
  .auth-card {
    border-radius: 12px;
  }

  .auth-page {
    padding: 12px;
  }
}
</style>
