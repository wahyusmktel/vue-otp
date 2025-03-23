<script setup>
import { ref } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'

const page = usePage()
const toast = page.props.toast
const filters = ref(usePage().props.filters || { search: '' })

const showModal = ref(false)
const isEdit = ref(false)
const selectedId = ref(null)

const form = useForm({
  nama_lokasi: '',
  luas: '',
  status_tanah: '',
  keterangan: '',
  dokumen: null,
})

const openModal = (item = null) => {
  showModal.value = true

  if (item) {
    // Mode Edit
    isEdit.value = true
    selectedId.value = item.id

    form.defaults({
      nama_lokasi: item.nama_lokasi,
      luas: item.luas,
      status_tanah: item.status_tanah,
      keterangan: item.keterangan,
      dokumen: null
    })

    form.reset()
  } else {
    // Mode Tambah Baru
    isEdit.value = false
    selectedId.value = null

    // ✅ Set default kosong dulu, lalu reset
    form.defaults({
      nama_lokasi: '',
      luas: '',
      status_tanah: '',
      keterangan: '',
      dokumen: null
    })

    form.reset()
  }
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submit = () => {
  if (isEdit.value) {
    form.post(route('data-tanah.update', selectedId.value), {
      method: 'put',
      forceFormData: true,
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('data-tanah.store'), {
      forceFormData: true,
      onSuccess: () => closeModal(),
    })
  }
}

const destroy = (id) => {
  if (confirm('Yakin ingin menghapus data ini?')) {
    router.delete(route('data-tanah.destroy', id))
  }
}

const search = () => {
  router.get(route('data-tanah.index'), {
    search: filters.value.search
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

</script>

<template>
  <Head title="Data Tanah" />
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Data Tanah SMK Telkom Lampung</h1>

    <div class="flex items-center justify-between mb-4">
    <input
        v-model="filters.search"
        @keyup.enter="search"
        type="text"
        placeholder="Cari lokasi atau status..."
        class="border px-3 py-2 rounded w-1/2"
    />
    <button @click="search" class="ml-2 bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">
        Cari
    </button>
    </div>

    <button @click="openModal()" class="mb-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
      + Tambah Data
    </button>

    <table class="w-full table-auto border">
      <thead class="bg-gray-200">
        <tr>
          <th class="p-2 border">#</th>
          <th class="p-2 border">Lokasi</th>
          <th class="p-2 border">Luas (m²)</th>
          <th class="p-2 border">Status</th>
          <th class="p-2 border">Keterangan</th>
          <th class="p-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in $page.props.dataTanah" :key="item.id" class="hover:bg-gray-50">
          <td class="p-2 border">{{ index + 1 }}</td>
          <td class="p-2 border">{{ item.nama_lokasi }}</td>
          <td class="p-2 border">{{ item.luas }}</td>
          <td class="p-2 border">{{ item.status_tanah }}</td>
          <td class="p-2 border">{{ item.keterangan }}</td>
          <td class="p-2 border">
            <button @click="openModal(item)" class="text-blue-600 hover:underline mr-2">Edit</button>
            <button @click="destroy(item.id)" class="text-red-600 hover:underline">Hapus</button>
            <a v-if="item.dokumen" :href="`/storage/${item.dokumen}`" target="_blank" class="text-blue-600 underline">
              Lihat Dokumen
            </a>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded shadow p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ isEdit ? 'Edit' : 'Tambah' }} Data Tanah</h2>

        <form @submit.prevent="submit">
          <div class="mb-3">
            <label>Nama Lokasi</label>
            <input v-model="form.nama_lokasi" type="text" class="w-full border p-2 rounded" />
            <div class="text-red-500 text-sm" v-if="form.errors.nama_lokasi">{{ form.errors.nama_lokasi }}</div>
          </div>

          <div class="mb-3">
            <label>Luas (m²)</label>
            <input v-model="form.luas" type="number" class="w-full border p-2 rounded" />
            <div class="text-red-500 text-sm" v-if="form.errors.luas">{{ form.errors.luas }}</div>
          </div>

          <div class="mb-3">
            <label>Status Tanah</label>
            <input v-model="form.status_tanah" type="text" class="w-full border p-2 rounded" />
            <div class="text-red-500 text-sm" v-if="form.errors.status_tanah">{{ form.errors.status_tanah }}</div>
          </div>

          <div class="mb-3">
            <label>Keterangan</label>
            <textarea v-model="form.keterangan" class="w-full border p-2 rounded"></textarea>
          </div>

          <div class="mb-3">
            <label>Upload Dokumen</label>
            <input type="file" @change="e => form.dokumen = e.target.files[0]" class="w-full border p-2 rounded" />
            <div class="text-red-500 text-sm" v-if="form.errors.dokumen">{{ form.errors.dokumen }}</div>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded">Batal</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="toast" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50">
      {{ toast }}
    </div>
  </div>
</template>
