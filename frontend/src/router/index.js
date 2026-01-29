import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/components/Login.vue'
import Register from '@/components/Register.vue'
import Dashboard from '@/components/Dashboard.vue'
import ProfilePage from '@/components/ProfilePage.vue'
import EditProfile from '@/components/EditProfile.vue'
import Settings from '@/components/Settings.vue'
import AdoptionApplicationForm from '@/components/AdoptionApplicationForm.vue'
import PetDetails from '@/components/PetDetails.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage
  },
  {
    path: '/profile/edit',
    name: 'EditProfile',
    component: EditProfile
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings
  },
  {
    path: '/apply/:petId',
    name: 'AdoptionApplication',
    component: AdoptionApplicationForm
  },
  {
    path: '/pets/:petId',
    name: 'PetDetails',
    component: PetDetails
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
