<template>
  <div class="w-full lg:w-80 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <!-- Applied Filters Header -->
    <div v-if="hasActiveFilters" class="mb-6 pb-6 border-b border-gray-200">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-900">Applied Filters</h3>
        <button @click="clearAllFilters" class="font-medium text-sm transition" style="color: #2F5597;" @mouseenter="$event.target.style.opacity='0.7'" @mouseleave="$event.target.style.opacity='1'">
          Clear All
        </button>
      </div>
      <div class="flex flex-wrap gap-2">
        <!-- Price Filter Badge -->
        <div v-if="isPriceFiltered" class="flex items-center gap-1 px-3 py-1 rounded-full text-sm" style="background-color: #cce4f4; color: #2F5597;">
          <span>${{ filters.priceMin }} - ${{ filters.priceMax }}</span>
          <button @click="clearPriceFilter" class="hover:font-semibold">×</button>
        </div>
        <!-- Vendor Filter Badges -->
        <div v-for="vendor in filters.vendors" :key="vendor" class="flex items-center gap-1 px-3 py-1 rounded-full text-sm" style="background-color: #cce4f4; color: #2F5597;">
          <span>{{ vendor }}</span>
          <button @click="removeVendor(vendor)" class="hover:font-semibold">×</button>
        </div>
        <!-- Category Filter Badges -->
        <div v-for="cat in filters.categories" :key="cat" class="flex items-center gap-1 px-3 py-1 rounded-full text-sm" style="background-color: #cce4f4; color: #2F5597;">
          <span>{{ cat }}</span>
          <button @click="removeCategory(cat)" class="hover:font-semibold">×</button>
        </div>
      </div>
    </div>

    <!-- Price Filter -->
    <div class="mb-6 pb-6 border-b border-gray-200">
      <button @click="toggleSection('price')" class="flex items-center justify-between w-full mb-3">
        <h4 class="font-semibold text-gray-900">Price</h4>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': openSections.price }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </button>
      <div v-show="openSections.price" class="space-y-3">
        <div class="grid grid-cols-2 gap-2 w-full">
          <div class="w-full">
            <label class="text-xs text-gray-600 font-medium block mb-1">MIN</label>
            <div class="flex items-center border border-gray-300 rounded px-2 py-2 w-full">
              <span class="text-gray-600">$</span>
              <input v-model.number="filters.priceMin" type="number" min="0" step="10" class="flex-1 ml-1 border-0 outline-none text-sm w-full" />
            </div>
          </div>
          <div class="w-full">
            <label class="text-xs text-gray-600 font-medium block mb-1">MAX</label>
            <div class="flex items-center border border-gray-300 rounded px-2 py-2 w-full">
              <span class="text-gray-600">$</span>
              <input v-model.number="filters.priceMax" type="number" min="0" step="10" class="flex-1 ml-1 border-0 outline-none text-sm w-full" />
            </div>
          </div>
        </div>
        <button @click="applyFilters" class="w-full text-white font-medium py-2 rounded text-sm transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Go
        </button>
      </div>
    </div>

    <!-- Vendors Filter -->
    <div v-if="normalizedVendors.length > 0" class="mb-6 pb-6 border-b border-gray-200">
      <button @click="toggleSection('vendors')" class="flex items-center justify-between w-full mb-3">
        <h4 class="font-semibold text-gray-900">Vendors</h4>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': openSections.vendors }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </button>
      <div v-show="openSections.vendors" class="space-y-3">
        <input 
          v-model="vendorSearch" 
          type="text" 
          placeholder="Search Vendor" 
          class="w-full px-3 py-2 border border-gray-300 rounded text-sm outline-none transition" @focus="$event.target.style.borderColor='#2F5597'" @blur="$event.target.style.borderColor='rgb(209, 213, 219)'"
        />
        <div class="space-y-2 max-h-64 overflow-y-auto">
          <label v-for="vendor in filteredVendors" :key="vendor.name" class="flex items-center gap-3 cursor-pointer">
            <input 
              :checked="filters.vendors.includes(vendor.name)" 
              @change="toggleVendor(vendor.name)"
              type="radio" 
              name="vendor-select"
              class="w-4 h-4 rounded-full border-gray-300 cursor-pointer" style="accent-color: #2F5597;"
            />
            <span class="text-sm text-gray-700">{{ vendor.name }} <span class="text-gray-500">({{ vendor.count }})</span></span>
          </label>
        </div>
      </div>
    </div>

    <!-- Categories Filter -->
    <div v-if="normalizedCategories.length > 0" class="mb-6 pb-6 border-b border-gray-200">
      <button @click="toggleSection('categories')" class="flex items-center justify-between w-full mb-3">
        <h4 class="font-semibold text-gray-900">Categories</h4>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': openSections.categories }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </button>
      <div v-show="openSections.categories" class="space-y-3">
        <input 
          v-model="categorySearch" 
          type="text" 
          placeholder="Search Categories" 
          class="w-full px-3 py-2 border border-gray-300 rounded text-sm outline-none transition" @focus="$event.target.style.borderColor='#2F5597'" @blur="$event.target.style.borderColor='rgb(209, 213, 219)'"
        />
        <div class="space-y-2 max-h-[36rem] overflow-y-auto">
          <label v-for="category in filteredCategories" :key="category.name" class="flex items-center gap-3 cursor-pointer">
            <input 
              :checked="filters.categories.includes(category.name)" 
              @change="toggleCategory(category.name)"
              type="radio" 
              name="category"
              class="w-4 h-4 rounded-full border-gray-300 cursor-pointer" style="accent-color: #2F5597;"
            />
            <span class="text-sm text-gray-700">{{ category.name }} <span class="text-gray-500">({{ category.count }})</span></span>
          </label>
        </div>
      </div>
    </div>

    <!-- Billing Model Filter -->
    <div class="mb-6 pb-6 border-b border-gray-200">
      <button @click="toggleSection('billing')" class="flex items-center justify-between w-full mb-3">
        <h4 class="font-semibold text-gray-900">Billing Model</h4>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': openSections.billing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </button>
      <div v-show="openSections.billing" class="space-y-2">
        <label v-for="model in billingModels" :key="model" class="flex items-center gap-3 cursor-pointer">
          <input 
            :checked="filters.billingModels.includes(model)" 
            @change="toggleBillingModel(model)"
            type="checkbox" 
            class="w-4 h-4 rounded border-gray-300 cursor-pointer" style="accent-color: #2F5597;"
          />
          <span class="text-sm text-gray-700">{{ model }}</span>
        </label>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  vendors: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['filter-change'])

const filters = ref({
  priceMin: 0,
  priceMax: 10000,
  vendors: [],
  categories: [],
  billingModels: []
})

const openSections = ref({
  price: true,
  vendors: true,
  categories: true,
  billing: false
})

const vendorSearch = ref('')
const categorySearch = ref('')

const billingModels = [
  'Fixed/Flat Fee',
  'Subscription',
  'Usage-Based',
  'Hybrid'
]

const hasDataCount = (item) => {
  const count = Number(item?.count ?? 0)
  return Number.isFinite(count) && count > 0
}

const normalizedVendors = computed(() => {
  return (props.vendors || []).filter(vendor => vendor?.name && hasDataCount(vendor))
})

const normalizedCategories = computed(() => {
  return (props.categories || []).filter(category => category?.name && hasDataCount(category))
})

const filteredVendors = computed(() => {
  if (!vendorSearch.value) return normalizedVendors.value
  return normalizedVendors.value.filter(v => v.name.toLowerCase().includes(vendorSearch.value.toLowerCase()))
})

const filteredCategories = computed(() => {
  if (!categorySearch.value) return normalizedCategories.value
  return normalizedCategories.value.filter(c => c.name.toLowerCase().includes(categorySearch.value.toLowerCase()))
})

const isPriceFiltered = computed(() => filters.value.priceMin > 0 || filters.value.priceMax < 10000)

const hasActiveFilters = computed(() => {
  return isPriceFiltered.value || filters.value.vendors.length > 0 || filters.value.categories.length > 0 || filters.value.billingModels.length > 0
})

const toggleSection = (section) => {
  openSections.value[section] = !openSections.value[section]
}

const toggleVendor = (vendor) => {
  // Single-select: clicking a vendor replaces the current selection
  if (filters.value.vendors.includes(vendor)) {
    filters.value.vendors = []
  } else {
    filters.value.vendors = [vendor]
  }
  applyFilters()
}

const removeVendor = (vendor) => {
  filters.value.vendors = filters.value.vendors.filter(v => v !== vendor)
  applyFilters()
}

const toggleCategory = (category) => {
  if (filters.value.categories.includes(category)) {
    filters.value.categories = []
  } else {
    filters.value.categories = [category]
  }
  applyFilters()
}

const removeCategory = (category) => {
  filters.value.categories = filters.value.categories.filter(c => c !== category)
  applyFilters()
}

const toggleBillingModel = (model) => {
  const index = filters.value.billingModels.indexOf(model)
  if (index > -1) {
    filters.value.billingModels.splice(index, 1)
  } else {
    filters.value.billingModels.push(model)
  }
  applyFilters()
}

const clearPriceFilter = () => {
  filters.value.priceMin = 0
  filters.value.priceMax = 10000
  applyFilters()
}

const clearAllFilters = () => {
  filters.value = {
    priceMin: 0,
    priceMax: 10000,
    vendors: [],
    categories: [],
    billingModels: []
  }
  vendorSearch.value = ''
  categorySearch.value = ''
  applyFilters()
}

const applyFilters = () => {
  emit('filter-change', { ...filters.value })
}
</script>
