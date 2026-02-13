import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/components/Login.vue'
import Register from '@/components/Register.vue'
import Dashboard from '@/components/Dashboard.vue'
import ProfilePage from '@/components/ProfilePage.vue'
import EditProfile from '@/components/EditProfile.vue'
import Settings from '@/components/Settings.vue'
import AdoptionApplicationForm from '@/components/AdoptionApplicationForm.vue'
import PetDetails from '@/components/PetDetails.vue'
import ShelterApplicationForm from '@/components/ShelterApplicationForm.vue'
import AddPet from '@/components/AddPet.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false, public: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false, public: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile/edit',
    name: 'EditProfile',
    component: EditProfile,
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: { requiresAuth: true }
  },
  {
    path: '/shelter-application',
    name: 'ShelterApplication',
    component: ShelterApplicationForm,
    meta: { requiresAuth: true, allowedRoles: ['user', 'admin'] }
  },
  {
    path: '/shelter/add-pet',
    name: 'AddPet',
    component: AddPet,
    meta: { requiresAuth: true, allowedRoles: ['shelter', 'admin'] }
  },
  {
    path: '/apply/:petId',
    name: 'AdoptionApplication',
    component: AdoptionApplicationForm,
    meta: { requiresAuth: true, allowedRoles: ['user', 'admin'] }
  },
  {
    path: '/pets/:petId',
    name: 'PetDetails',
    component: PetDetails,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Helper function to verify token with backend
async function verifyToken(token) {
  try {
    const response = await fetch('http://localhost:8000/api/user', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      const user = await response.json()
      return user
    }
    return null
  } catch (error) {
    console.error('Token verification error:', error)
    return null
  }
}

// Main route guard for authentication and authorization
router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('token')
  const user = JSON.parse(localStorage.getItem('user') || 'null')
  const requiresAuth = to.meta.requiresAuth
  const isPublic = to.meta.public
  const allowedRoles = to.meta.allowedRoles

  // If route is public (login/register)
  if (isPublic) {
    // If user is already logged in, redirect to dashboard
    if (token) {
      next('/dashboard')
      return
    }
    next()
    return
  }

  // If route requires authentication
  if (requiresAuth) {
    if (!token) {
      // No token, redirect to login
      next('/')
      return
    }

    // Verify token is still valid
    const verifiedUser = await verifyToken(token)
    
    if (!verifiedUser) {
      // Token is invalid, logout and redirect to login
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      next('/')
      return
    }

    // Token is valid, update user in storage if needed
    localStorage.setItem('user', JSON.stringify(verifiedUser))

    // Check role-based access
    if (allowedRoles && !allowedRoles.includes(verifiedUser.role)) {
      // User role is not allowed for this route
      console.warn(`User role '${verifiedUser.role}' not allowed for route '${to.name}'`)
      next('/dashboard')
      return
    }

    next()
    return
  }

  // All other routes
  next()
})

// Handle back button attempts to login while authenticated
let lastPath = '/'

router.afterEach((to, from) => {
  lastPath = to.path
})

window.addEventListener('popstate', () => {
  const token = localStorage.getItem('token')
  const currentPath = window.location.pathname

  // If user is logged in and tries to go back to login/register, prevent it
  if (token && (currentPath === '/' || currentPath === '/register')) {
    // Push current location back to history and redirect to dashboard
    window.history.pushState(null, '', lastPath)
    window.location.href = '/dashboard'
  }
})

export default router
