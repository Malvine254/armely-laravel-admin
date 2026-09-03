<template>
  <div class="w-full lg:w-80 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col" :class="compact ? 'p-4' : 'p-6'">
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
          <span>{{ priceFilterLabel }}</span>
          <button @click="clearPriceFilter" class="hover:font-semibold">×</button>
        </div>
        <!-- Part Number Filter Badge -->
        <div v-if="filters.partNumber" class="flex items-center gap-1 px-3 py-1 rounded-full text-sm" style="background-color: #cce4f4; color: #2F5597;">
          <span>Part: {{ filters.partNumber }}</span>
          <button @click="clearPartNumberFilter" class="hover:font-semibold">×</button>
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
        <div v-if="filters.productType" class="flex items-center gap-1 px-3 py-1 rounded-full text-sm" style="background-color: #cce4f4; color: #2F5597;">
          <span>{{ filters.productType === 'hardware' ? 'Hardware' : 'Software' }}</span>
          <button @click="clearProductType" aria-label="Remove product type filter" class="hover:font-semibold">×</button>
        </div>
        <div v-for="status in filters.lifecycleStatuses" :key="`lifecycle-${status}`" class="flex items-center gap-1 px-3 py-1 rounded-full text-sm" style="background-color: #cce4f4; color: #2F5597;">
          <span>{{ status }}</span>
          <button @click="toggleLifecycleStatus(status)" :aria-label="`Remove ${status} filter`" class="hover:font-semibold">×</button>
        </div>
        <!-- Review Rating / Image Filter Badges -->
        <div
          v-for="status in filters.mediaStatuses"
          :key="`media-${status}`"
          class="flex items-center gap-1 px-3 py-1 rounded-full text-sm"
          :style="status === 'No Images' ? 'background-color: #f5f3ff; color: #9333ea;' : 'background-color: #eef7ec; color: #1f6e3e;'"
        >
          <span>{{ status }}</span>
          <button @click="toggleMediaStatus(status)" class="hover:font-semibold">×</button>
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
              <input v-model.number="filters.priceMin" type="number" :min="DEFAULT_MIN_PRICE" step="10" class="flex-1 ml-1 border-0 outline-none text-sm w-full" />
            </div>
          </div>
          <div class="w-full">
            <label class="text-xs text-gray-600 font-medium block mb-1">MAX</label>
            <div class="flex items-center border border-gray-300 rounded px-2 py-2 w-full">
              <span class="text-gray-600">$</span>
              <input v-model.number="filters.priceMax" type="number" min="0" step="10" placeholder="No max" class="flex-1 ml-1 border-0 outline-none text-sm w-full" />
            </div>
          </div>
        </div>
        <button @click="applyFilters" class="w-full text-white font-medium py-2 rounded text-sm transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Go
        </button>
      </div>
    </div>

    <!-- Categories Filter -->
    <div class="mb-6 pb-6 border-b border-gray-200">
      <button @click="toggleSection('categories')" class="flex items-center justify-between w-full mb-3">
        <h4 class="font-semibold text-gray-900">Categories</h4>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': openSections.categories }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </button>
      <div v-show="openSections.categories" class="space-y-3">
        <!-- Skeleton while loading -->
        <template v-if="categoriesLoading">
          <div v-for="i in 6" :key="'cat-skel-'+i" class="flex items-center gap-3 animate-pulse">
            <div class="w-4 h-4 rounded-full bg-gray-200"></div>
            <div class="h-4 bg-gray-200 rounded" :style="{ width: (50 + i * 8) + '%' }"></div>
          </div>
        </template>
        <p v-else-if="normalizedCategories.length === 0" class="rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-500">
          No matching categories
        </p>
        <template v-else>
          <input 
            v-model="categorySearch" 
            type="text" 
            placeholder="Search Categories" 
            class="w-full px-3 py-2 border border-gray-300 rounded text-sm outline-none transition" @focus="$event.target.style.borderColor='#2F5597'" @blur="$event.target.style.borderColor='rgb(209, 213, 219)'"
          />
          <div class="facet-section-scroll min-w-0 space-y-2 pr-2" :class="compact ? 'max-h-24' : 'max-h-72'">
            <label v-for="category in filteredCategories" :key="category.name" class="flex min-w-0 items-center gap-3 cursor-pointer" :title="category.name">
              <input 
                :checked="filters.categories.includes(category.name)" 
                @change="toggleCategory(category.name)"
                type="radio" 
                name="category"
                class="w-4 h-4 rounded-full border-gray-300 cursor-pointer" style="accent-color: #2F5597;"
              />
              <span class="min-w-0 flex-1 truncate text-sm text-gray-700">{{ category.name }}</span>
              <span v-if="hasDisplayableCount(category)" class="flex-shrink-0 text-xs text-gray-500">({{ category.count }})</span>
            </label>
          </div>
        </template>
      </div>
    </div>

    <!-- Vendors Filter -->
    <div class="mb-6 pb-6 border-b border-gray-200">
      <button @click="toggleSection('vendors')" class="flex items-center justify-between w-full mb-3">
        <h4 class="font-semibold text-gray-900">Vendors</h4>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': openSections.vendors }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </button>
      <div v-show="openSections.vendors" class="space-y-3">
        <!-- Skeleton while loading -->
        <template v-if="vendorsLoading">
          <div v-for="i in 8" :key="'ven-skel-'+i" class="flex items-center gap-3 animate-pulse">
            <div class="w-4 h-4 rounded-full bg-gray-200"></div>
            <div class="h-4 bg-gray-200 rounded" :style="{ width: (40 + i * 6) + '%' }"></div>
          </div>
        </template>
        <p v-else-if="normalizedVendors.length === 0" class="rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-500">
          No matching vendors
        </p>
        <template v-else>
          <input 
            v-model="vendorSearch" 
            type="text" 
            placeholder="Search Vendor" 
            class="w-full px-3 py-2 border border-gray-300 rounded text-sm outline-none transition" @focus="$event.target.style.borderColor='#2F5597'" @blur="$event.target.style.borderColor='rgb(209, 213, 219)'"
          />
          <div class="facet-section-scroll min-w-0 space-y-2 pr-2" :class="compact ? 'max-h-24' : 'max-h-72'">
            <label v-for="vendor in filteredVendors" :key="vendor.name" class="flex min-w-0 items-center gap-3 cursor-pointer" :title="vendor.name">
              <input 
                :checked="filters.vendors.includes(vendor.name)" 
                @change="toggleVendor(vendor.name)"
                type="radio" 
                name="vendor-select"
                class="w-4 h-4 rounded-full border-gray-300 cursor-pointer" style="accent-color: #2F5597;"
              />
              <span class="min-w-0 flex-1 truncate text-sm text-gray-700">{{ vendor.name }}</span>
              <span v-if="hasDisplayableCount(vendor)" class="flex-shrink-0 text-xs text-gray-500">({{ vendor.count }})</span>
            </label>
          </div>
        </template>
      </div>
    </div>

    <!-- Part Number Filter -->
    <div v-if="!compact" class="mb-6 pb-6 border-b border-gray-200">
      <button @click="toggleSection('partNumber')" class="flex items-center justify-between w-full mb-3">
        <h4 class="font-semibold text-gray-900">Part Number</h4>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': openSections.partNumber }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </button>
      <div v-show="openSections.partNumber" class="space-y-3">
        <input
          v-model="filters.partNumber"
          type="text"
          placeholder="e.g. ABC-123"
          class="w-full px-3 py-2 border border-gray-300 rounded text-sm outline-none transition"
          @focus="$event.target.style.borderColor='#2F5597'"
          @blur="$event.target.style.borderColor='rgb(209, 213, 219)'"
          @keyup.enter="applyFilters"
        />
        <button @click="applyFilters" class="w-full text-white font-medium py-2 rounded text-sm transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Apply Part Filter
        </button>
      </div>
    </div>

    <!-- Review Rating Filter -->
    <div v-if="!compact" class="mb-6 pb-6 border-b border-gray-200">
      <button @click="toggleSection('media')" class="flex items-center justify-between w-full mb-3">
        <h4 class="font-semibold text-gray-900">Review Rating</h4>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': openSections.media }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </button>
      <div v-show="openSections.media" class="space-y-3">
        <div class="flex flex-wrap gap-2">
          <button
            @click="setFiveStar"
            class="px-3 py-1.5 text-xs font-semibold rounded-full border transition"
            style="border-color: #1f6e3e; color: #1f6e3e;"
            @mouseenter="$event.target.style.backgroundColor='#edf8f2'"
            @mouseleave="$event.target.style.backgroundColor='transparent'"
          >
            5 Stars
          </button>
          <button
            @click="setFourStarPlus"
            class="px-3 py-1.5 text-xs font-semibold rounded-full border transition"
            style="border-color: #1f6e3e; color: #1f6e3e;"
            @mouseenter="$event.target.style.backgroundColor='#edf8f2'"
            @mouseleave="$event.target.style.backgroundColor='transparent'"
          >
            4+ Stars
          </button>
          <button
            @click="setHasReviews"
            class="px-3 py-1.5 text-xs font-semibold rounded-full border transition"
            style="border-color: #1f6e3e; color: #1f6e3e;"
            @mouseenter="$event.target.style.backgroundColor='#edf8f2'"
            @mouseleave="$event.target.style.backgroundColor='transparent'"
          >
            Has Reviews
          </button>
          <button
            v-if="showImageFilters"
            @click="setHasImages"
            class="px-3 py-1.5 text-xs font-semibold rounded-full border transition"
            style="border-color: #2F5597; color: #2F5597;"
            @mouseenter="$event.target.style.backgroundColor='#eef4ff'"
            @mouseleave="$event.target.style.backgroundColor='transparent'"
          >
            Has Images
          </button>
          <button
            v-if="showImageFilters"
            @click="setNoImages"
            class="px-3 py-1.5 text-xs font-semibold rounded-full border transition"
            style="border-color: #9333ea; color: #9333ea;"
            @mouseenter="$event.target.style.backgroundColor='#f5f3ff'"
            @mouseleave="$event.target.style.backgroundColor='transparent'"
          >
            No Images
          </button>
          <button
            @click="clearMedia"
            class="px-3 py-1.5 text-xs font-semibold rounded-full border transition"
            style="border-color: #d1d5db; color: #4b5563;"
            @mouseenter="$event.target.style.backgroundColor='#f3f4f6'"
            @mouseleave="$event.target.style.backgroundColor='transparent'"
          >
            Clear
          </button>
        </div>
        <label v-for="status in mediaOptions" :key="status.name" class="flex items-center justify-between gap-3 cursor-pointer">
          <div class="flex items-center gap-3">
            <input
              :checked="filters.mediaStatuses.includes(status.name)"
              @change="toggleMediaStatus(status.name)"
              type="checkbox"
              class="w-4 h-4 rounded border-gray-300 cursor-pointer" style="accent-color: #1f6e3e;"
            />
            <span class="text-sm text-gray-700">{{ status.name }}</span>
          </div>
          <span class="text-xs text-gray-500">{{ status.count }}</span>
        </label>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  vendors: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  activeFilters: {
    type: Object,
    default: () => ({})
  },
  lifecycleOptions: {
    type: Array,
    default: () => [
      { name: 'Active', count: 0 },
      { name: 'End of Life', count: 0 }
    ]
  },
  mediaOptions: {
    type: Array,
    default: () => [
      { name: '5 Stars', count: 0 },
      { name: '4 Stars & Up', count: 0 },
      { name: '3 Stars & Up', count: 0 },
      { name: 'Has Reviews', count: 0 }
    ]
  },
  compact: {
    type: Boolean,
    default: false
  },
  vendorsLoading: {
    type: Boolean,
    default: false
  },
  categoriesLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['filter-change', 'clear-all'])
const showImageFilters = !['false', '0', 'off', 'no'].includes(
  String(import.meta.env.VITE_SHOW_IMAGE_FILTERS ?? 'true').trim().toLowerCase()
)
const DEFAULT_MIN_PRICE = Number(import.meta.env.VITE_MIN_PRICE ?? 100)
const DEFAULT_MAX_PRICE = 0
const POPULAR_VENDOR_LIMIT = 40
const POPULAR_CATEGORY_LIMIT = 40

const filters = ref({
  priceMin: DEFAULT_MIN_PRICE,
  priceMax: DEFAULT_MAX_PRICE,
  partNumber: '',
  productType: '',
  vendors: [],
  categories: [],
  lifecycleStatuses: [],
  mediaStatuses: []
})

const syncFromActiveFilters = (source = {}) => {
  const next = source || {}
  filters.value = {
    priceMin: Math.max(DEFAULT_MIN_PRICE, Number(next.priceMin ?? DEFAULT_MIN_PRICE)),
    priceMax: Math.max(0, Number(next.priceMax ?? DEFAULT_MAX_PRICE)),
    partNumber: String(next.partNumber ?? ''),
    productType: String(next.productType ?? ''),
    vendors: Array.isArray(next.vendors) ? [...next.vendors] : [],
    categories: Array.isArray(next.categories) ? [...next.categories] : [],
    lifecycleStatuses: Array.isArray(next.lifecycleStatuses) ? [...next.lifecycleStatuses] : [],
    mediaStatuses: Array.isArray(next.mediaStatuses)
      ? next.mediaStatuses.filter((status) => showImageFilters || !['Has Images', 'No Images'].includes(status))
      : []
  }
}

watch(
  () => props.activeFilters,
  (next) => {
    syncFromActiveFilters(next)
  },
  { immediate: true, deep: true }
)

const openSections = ref({
  price: true,
  partNumber: true,
  vendors: true,
  categories: true,
  lifecycle: true,
  media: true
})

const vendorSearch = ref('')
const categorySearch = ref('')

const hasDataCount = (item) => {
  const count = Number(item?.count ?? 0)
  return Number.isFinite(count) && count > 0
}

const hasDisplayableCount = (item) => {
  const count = Number(item?.count)
  return Number.isFinite(count) && count >= 0
}

const normalizedVendors = computed(() => {
  return (props.vendors || []).filter(vendor => vendor?.name)
})

const normalizedCategories = computed(() => {
  return (props.categories || []).filter(category => category?.name)
})

const filteredVendors = computed(() => {
  const query = vendorSearch.value.trim().toLowerCase()
  const candidates = normalizedVendors.value
    .filter((vendor) => !query || vendor.name.toLowerCase().includes(query))
    .sort((left, right) => {
      const countDifference = Number(right.count || 0) - Number(left.count || 0)
      return countDifference || left.name.localeCompare(right.name)
    })

  const visible = candidates.slice(0, POPULAR_VENDOR_LIMIT)
  const visibleNames = new Set(visible.map((vendor) => vendor.name.toLowerCase()))

  // Keep an active vendor available even when it is outside the popularity cap.
  candidates.forEach((vendor) => {
    if (
      filters.value.vendors.includes(vendor.name)
      && !visibleNames.has(vendor.name.toLowerCase())
    ) {
      visible.push(vendor)
      visibleNames.add(vendor.name.toLowerCase())
    }
  })

  return visible
})

const filteredCategories = computed(() => {
  const query = categorySearch.value.trim().toLowerCase()
  return normalizedCategories.value
    .filter((category) => !query || category.name.toLowerCase().includes(query))
    .sort((left, right) => Number(right.count || 0) - Number(left.count || 0) || left.name.localeCompare(right.name))
    .slice(0, POPULAR_CATEGORY_LIMIT)
})

const isPriceFiltered = computed(() => filters.value.priceMin > DEFAULT_MIN_PRICE || filters.value.priceMax > 0)

const priceFilterLabel = computed(() => {
  const min = Math.max(DEFAULT_MIN_PRICE, Number(filters.value.priceMin || DEFAULT_MIN_PRICE))
  const max = Math.max(0, Number(filters.value.priceMax || 0))

  return max > 0 ? `$${min} - $${max}` : `$${min}+`
})

const hasActiveFilters = computed(() => {
  return isPriceFiltered.value
    || !!filters.value.partNumber
    || !!filters.value.productType
    || filters.value.vendors.length > 0
    || filters.value.categories.length > 0
    || filters.value.lifecycleStatuses.length > 0
    || filters.value.mediaStatuses.length > 0
})

const clearProductType = () => {
  filters.value.productType = ''
  applyFilters()
}

const clearPartNumberFilter = () => {
  filters.value.partNumber = ''
  applyFilters()
}

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

const toggleLifecycleStatus = (status) => {
  const index = filters.value.lifecycleStatuses.indexOf(status)
  if (index > -1) {
    filters.value.lifecycleStatuses.splice(index, 1)
  } else {
    filters.value.lifecycleStatuses.push(status)
  }
  applyFilters()
}

const setActiveLifecycle = () => {
  filters.value.lifecycleStatuses = ['Active']
  applyFilters()
}

const setEolLifecycle = () => {
  filters.value.lifecycleStatuses = ['End of Life']
  applyFilters()
}

const clearLifecycle = () => {
  filters.value.lifecycleStatuses = []
  applyFilters()
}

const toggleMediaStatus = (status) => {
  const index = filters.value.mediaStatuses.indexOf(status)
  if (index > -1) {
    filters.value.mediaStatuses.splice(index, 1)
  } else {
    filters.value.mediaStatuses.push(status)
  }
  applyFilters()
}

const setFiveStar = () => {
  filters.value.mediaStatuses = ['5 Stars']
  applyFilters()
}

const setFourStarPlus = () => {
  filters.value.mediaStatuses = ['4 Stars & Up']
  applyFilters()
}

const setHasReviews = () => {
  filters.value.mediaStatuses = ['Has Reviews']
  applyFilters()
}

const setNoImages = () => {
  filters.value.mediaStatuses = ['No Images']
  applyFilters()
}

const setHasImages = () => {
  filters.value.mediaStatuses = ['Has Images']
  applyFilters()
}

const clearMedia = () => {
  filters.value.mediaStatuses = []
  applyFilters()
}

const clearPriceFilter = () => {
  filters.value.priceMin = DEFAULT_MIN_PRICE
  filters.value.priceMax = DEFAULT_MAX_PRICE
  applyFilters()
}

const clearAllFilters = () => {
  filters.value = {
    priceMin: DEFAULT_MIN_PRICE,
    priceMax: DEFAULT_MAX_PRICE,
    partNumber: '',
    productType: '',
    vendors: [],
    categories: [],
    lifecycleStatuses: [],
    mediaStatuses: []
  }
  vendorSearch.value = ''
  categorySearch.value = ''
  emit('clear-all')
}

const applyFilters = () => {
  filters.value.priceMin = Math.max(DEFAULT_MIN_PRICE, Number(filters.value.priceMin || DEFAULT_MIN_PRICE))
  filters.value.priceMax = Math.max(0, Number(filters.value.priceMax || 0))
  if (filters.value.priceMax > 0 && filters.value.priceMax < filters.value.priceMin) {
    filters.value.priceMax = filters.value.priceMin
  }

  emit('filter-change', { ...filters.value })
}
</script>
