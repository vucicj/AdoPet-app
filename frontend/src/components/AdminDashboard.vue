<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
  totalAccounts: 0,
  totalShelters: 0,
  totalPets: 0,
  totalAdoptions: 0
})
const recentAccounts = ref([])
const pets = ref([])
const allAccounts = ref([])
const loading = ref(true)
const error = ref('')
const viewingPets = ref(false)
const viewingAccounts = ref(false)
const petsLoading = ref(false)
const accountsLoading = ref(false)
const showDeleteModal = ref(false)
const userToDelete = ref(null)
const viewingAdoptions = ref(false)
const adoptions = ref([])
const adoptionsLoading = ref(false)
const showCreateShelterModal = ref(false)
const shelterForm = ref({ name: '', email: '', password: '' })
const shelterFormError = ref('')
const shelterFormLoading = ref(false)

const fetchStats = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/admin/stats`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      stats.value = await response.json()
    } else {
      error.value = 'Failed to load statistics'
    }
  } catch (err) {
    console.error('Error fetching stats:', err)
    error.value = 'Error loading statistics'
  }
}

const fetchRecentAccounts = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/admin/recent-accounts`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      recentAccounts.value = await response.json()
    }
  } catch (err) {
    console.error('Error fetching recent accounts:', err)
  }
}

const fetchPets = async () => {
  petsLoading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/admin/pets`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      pets.value = await response.json()
      viewingPets.value = true
    }
  } catch (err) {
    console.error('Error fetching pets:', err)
  } finally {
    petsLoading.value = false
  }
}

const handlePetsClick = async () => {
  viewingAccounts.value = false
  viewingPets.value = true
  await fetchPets()
}

const fetchAllAccounts = async () => {
  accountsLoading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/admin/users`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    if (response.ok) {
      allAccounts.value = await response.json()
    }
  } catch (err) {
    console.error('Error fetching all accounts:', err)
  } finally {
    accountsLoading.value = false
  }
}

const handleAccountsClick = async () => {
  viewingPets.value = false
  viewingAccounts.value = true
  viewingAdoptions.value = false
  await fetchAllAccounts()
}

const fetchAdoptions = async () => {
  adoptionsLoading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/admin/adoptions`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
    })
    if (response.ok) {
      adoptions.value = await response.json()
    }
  } catch (err) {
    console.error('Error fetching adoptions:', err)
  } finally {
    adoptionsLoading.value = false
  }
}

const openCreateShelter = () => {
  shelterForm.value = { name: '', email: '', password: '' }
  shelterFormError.value = ''
  showCreateShelterModal.value = true
}

const createShelter = async () => {
  shelterFormError.value = ''
  if (!shelterForm.value.name || !shelterForm.value.email || !shelterForm.value.password) {
    shelterFormError.value = 'All fields are required.'
    return
  }
  shelterFormLoading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/admin/shelters`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
      body: JSON.stringify(shelterForm.value)
    })
    const data = await response.json()
    if (response.ok) {
      showCreateShelterModal.value = false
      stats.value.totalAccounts += 1
      if (viewingAccounts.value) await fetchAllAccounts()
    } else {
      shelterFormError.value = data.message || Object.values(data.errors || {}).flat().join(' ') || 'Failed to create shelter.'
    }
  } catch (err) {
    shelterFormError.value = 'Network error. Try again.'
  } finally {
    shelterFormLoading.value = false
  }
}

const handleAdoptionsClick = async () => {
  viewingPets.value = false
  viewingAccounts.value = false
  viewingAdoptions.value = true
  await fetchAdoptions()
}

const confirmDeleteUser = (account) => {
  userToDelete.value = account
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  userToDelete.value = null
}

const deleteUser = async () => {
  if (!userToDelete.value) return
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/api/admin/users/${userToDelete.value.id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    if (response.ok) {
      allAccounts.value = allAccounts.value.filter(a => a.id !== userToDelete.value.id)
      stats.value.totalAccounts = Math.max(0, stats.value.totalAccounts - 1)
      cancelDelete()
    }
  } catch (err) {
    console.error('Error deleting user:', err)
  }
}

onMounted(async () => {
  await Promise.all([fetchStats(), fetchRecentAccounts()])
  loading.value = false
})
</script>

<template>
  <div class="admin-dashboard">
    <section class="hero hero-admin">
      <div class="hero-content">
        <h1 class="hero-title">Admin Dashboard</h1>
        <p class="hero-subtitle">Manage users, shelters, and oversee all adoptions</p>
        <button class="create-shelter-btn" @click="openCreateShelter">+ Create Shelter Account</button>
      </div>
    </section>

    <div v-if="error" class="error-banner">
      {{ error }}
    </div>

    <div v-if="loading" class="loading">
      <p>Loading admin data...</p>
    </div>

    <div v-else class="admin-content">
      <div class="stats-grid">
        <div class="stat-card clickable-card" @click="handleAccountsClick">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <h3>Total Accounts</h3>
            <p class="stat-number">{{ stats.totalAccounts }}</p>
          </div>
        </div>
        <div class="stat-card clickable-card" @click="handlePetsClick">
          <div class="stat-icon">🐾</div>
          <div class="stat-info">
            <h3>Total Pets</h3>
            <p class="stat-number">{{ stats.totalPets }}</p>
          </div>
        </div>
        <div class="stat-card clickable-card" @click="handleAdoptionsClick">
          <div class="stat-icon">❤️</div>
          <div class="stat-info">
            <h3>Adoptions</h3>
            <p class="stat-number">{{ stats.totalAdoptions }}</p>
          </div>
        </div>
      </div>

      <div class="admin-sections">
        <div v-if="viewingPets" class="admin-section">
          <div class="section-header">
            <h2>Total Pets</h2>
            <button class="close-btn" @click="viewingPets = false">✕</button>
          </div>
          <div class="admin-table">
            <div v-if="petsLoading" class="loading-small">Loading pets...</div>
            <table v-else-if="pets.length > 0">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Breed</th>
                  <th>Age</th>
                  <th>Location</th>
                  <th>Status</th>
                  <th>Shelter</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="pet in pets" :key="pet.id">
                  <td>{{ pet.name }}</td>
                  <td>{{ pet.breed }}</td>
                  <td>{{ pet.age }}</td>
                  <td>{{ pet.location }}</td>
                  <td>
                    <span class="status-badge" :class="`status-${pet.status}`">
                      {{ pet.status }}
                    </span>
                  </td>
                  <td>{{ pet.shelter?.name || 'N/A' }}</td>
                </tr>
              </tbody>
            </table>
            <p v-else class="placeholder-text">No pets found</p>
          </div>
        </div>

        <div v-if="viewingAdoptions" class="admin-section">
          <div class="section-header">
            <h2>Completed Adoptions</h2>
            <button class="close-btn" @click="viewingAdoptions = false">✕</button>
          </div>
          <div class="admin-table">
            <div v-if="adoptionsLoading" class="loading-small">Loading adoptions...</div>
            <table v-else-if="adoptions.length > 0">
              <thead>
                <tr>
                  <th>Adopter</th>
                  <th>Email</th>
                  <th>Pet</th>
                  <th>Breed</th>
                  <th>Species</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="adoption in adoptions" :key="adoption.id">
                  <td>{{ adoption.adopter_name }}</td>
                  <td>{{ adoption.adopter_email }}</td>
                  <td>{{ adoption.pet_name }}</td>
                  <td>{{ adoption.pet_breed }}</td>
                  <td>
                    <span class="status-badge status-available">{{ adoption.pet_species }}</span>
                  </td>
                  <td>{{ adoption.adopted_at }}</td>
                </tr>
              </tbody>
            </table>
            <p v-else class="placeholder-text">No completed adoptions yet</p>
          </div>
        </div>

        <div v-if="viewingAccounts" class="admin-section">
          <div class="section-header">
            <h2>Total Accounts</h2>
            <button class="close-btn" @click="viewingAccounts = false">✕</button>
          </div>
          <div class="admin-table">
            <div v-if="accountsLoading" class="loading-small">Loading accounts...</div>
            <table v-else-if="allAccounts.length > 0">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Applications</th>
                  <th>Joined</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="account in allAccounts" :key="account.id">
                  <td>{{ account.name }}</td>
                  <td>{{ account.email }}</td>
                  <td>
                    <span class="role-badge" :class="`role-${account.role}`">
                      {{ account.role.toUpperCase() }}
                    </span>
                  </td>
                  <td>{{ account.applications_count ?? '-' }}</td>
                  <td>{{ account.created_at }}</td>
                  <td>
                    <button class="delete-btn" @click="confirmDeleteUser(account)">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-else class="placeholder-text">No accounts found</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Shelter Modal -->
  <div v-if="showCreateShelterModal" class="modal-overlay" @click="showCreateShelterModal = false">
    <div class="modal-box shelter-modal" @click.stop>
      <div class="modal-icon">🏠</div>
      <h2 class="modal-title">Create Shelter Account</h2>
      <div v-if="shelterFormError" class="shelter-error">{{ shelterFormError }}</div>
      <div class="shelter-form">
        <div class="shelter-field">
          <label>Shelter Name</label>
          <input v-model="shelterForm.name" type="text" placeholder="e.g. Happy Paws Shelter" class="shelter-input" />
        </div>
        <div class="shelter-field">
          <label>Email</label>
          <input v-model="shelterForm.email" type="email" placeholder="shelter@example.com" class="shelter-input" />
        </div>
        <div class="shelter-field">
          <label>Password</label>
          <input v-model="shelterForm.password" type="password" placeholder="Min. 6 characters" class="shelter-input" />
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn-cancel" @click="showCreateShelterModal = false">Cancel</button>
        <button class="btn-create-shelter" @click="createShelter" :disabled="shelterFormLoading">
          {{ shelterFormLoading ? 'Creating...' : 'Create' }}
        </button>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div v-if="showDeleteModal" class="modal-overlay" @click="cancelDelete">
    <div class="modal-box" @click.stop>
      <div class="modal-icon">🗑️</div>
      <h2 class="modal-title">Delete User</h2>
      <p class="modal-text">
        Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>?<br/>
        This action cannot be undone.
      </p>
      <div class="modal-actions">
        <button class="btn-cancel" @click="cancelDelete">Cancel</button>
        <button class="btn-confirm-delete" @click="deleteUser">Delete</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hero-admin {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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

.admin-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px 24px;
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

.stat-card.clickable-card {
  cursor: pointer;
}

.stat-card.clickable-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  background: #fff8f8;
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

.admin-sections {
  display: grid;
  gap: 24px;
}

.admin-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.admin-section h2 {
  margin: 0 0 16px;
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-header h2 {
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #6b7280;
  padding: 0;
  line-height: 1;
}

.close-btn:hover {
  color: #1f2937;
}

.admin-table {
  overflow-x: auto;
}

.admin-table table {
  width: 100%;
  border-collapse: collapse;
}

.admin-table th {
  text-align: left;
  padding: 12px;
  background: #f9fafb;
  font-weight: 600;
  color: #374151;
  font-size: 14px;
  border-bottom: 2px solid #e5e7eb;
}

.admin-table td {
  padding: 12px;
  border-bottom: 1px solid #e5e7eb;
  color: #4b5563;
  font-size: 14px;
}

.admin-table tbody tr:hover {
  background: #f9fafb;
}

.role-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.role-user {
  background: #dbeafe;
  color: #1e40af;
}

.role-shelter {
  background: #d1fae5;
  color: #065f46;
}

.role-admin {
  background: #fee2e2;
  color: #991b1b;
}

.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: capitalize;
}

.status-available {
  background: #d1fae5;
  color: #065f46;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-adopted {
  background: #dbeafe;
  color: #1e40af;
}

.error-banner {
  background: #fee;
  color: #c00;
  padding: 12px 24px;
  text-align: center;
  font-size: 14px;
}

.loading {
  text-align: center;
  padding: 48px;
  color: #6b7280;
}

.placeholder-text {
  color: #9ca3af;
  font-size: 14px;
  text-align: center;
  padding: 48px;
}

.loading-small {
  text-align: center;
  padding: 24px;
  color: #6b7280;
  font-size: 14px;
}

.delete-btn {
  padding: 4px 12px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.delete-btn:hover {
  background: #dc2626;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal-box {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  width: 100%;
  max-width: 420px;
  text-align: center;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
  animation: popIn 0.2s ease;
}

@keyframes popIn {
  from { opacity: 0; transform: scale(0.92); }
  to   { opacity: 1; transform: scale(1); }
}

.modal-icon {
  font-size: 3rem;
  margin-bottom: 0.75rem;
}

.modal-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem;
}

.modal-text {
  color: #6b7280;
  font-size: 0.95rem;
  line-height: 1.6;
  margin: 0 0 1.5rem;
}

.modal-text strong {
  color: #1f2937;
}

.modal-actions {
  display: flex;
  gap: 1rem;
}

.btn-cancel {
  flex: 1;
  padding: 0.7rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  color: #374151;
  transition: background 0.2s;
}

.btn-cancel:hover {
  background: #f3f4f6;
}

.btn-confirm-delete {
  flex: 1;
  padding: 0.7rem;
  border: none;
  background: #ef4444;
  color: white;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-confirm-delete:hover {
  background: #dc2626;
}

.create-shelter-btn {
  margin-top: 20px;
  padding: 10px 24px;
  background: white;
  color: #dc2626;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.create-shelter-btn:hover {
  background: #f9fafb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.shelter-modal {
  text-align: left;
  max-width: 460px;
}

.shelter-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin: 16px 0 20px;
}

.shelter-field {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.shelter-field label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
}

.shelter-input {
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
}

.shelter-input:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
}

.shelter-error {
  background: #fee2e2;
  color: #991b1b;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 4px;
}

.btn-create-shelter {
  flex: 1;
  padding: 0.7rem;
  border: none;
  background: #ef4444;
  color: white;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-create-shelter:hover:not(:disabled) {
  background: #dc2626;
}

.btn-create-shelter:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 32px;
  }
}
</style>
