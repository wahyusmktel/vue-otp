<script setup>
import { ref, onMounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
  phone: String,
})

const form = useForm({
  phone: props.phone,
  otp: '',
})

const countdown = ref(300)
const canResend = ref(false)

const startCountdown = () => {
  countdown.value = 300
  canResend.value = false
  const timer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(timer)
      canResend.value = true
    }
  }, 1000)
}

onMounted(() => {
  startCountdown()
})

const resendOtp = () => {
  form.post(route('verification.resend'), {
    preserveScroll: true,
    onSuccess: () => {
      startCountdown()
    }
  })
}
</script>

<template>
  <Head title="Verifikasi OTP" />
  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Verifikasi OTP</h2>
    <p class="mb-4">Masukkan 6 digit kode OTP yang dikirim ke nomor <strong>{{ props.phone }}</strong>.</p>

    <form @submit.prevent="form.post('/verify-otp')">
      <input
        v-model="form.otp"
        type="text"
        placeholder="Masukkan OTP"
        maxlength="6"
        class="w-full p-2 border border-gray-300 rounded mb-2"
      />
      <div v-if="form.errors.otp" class="text-red-500 text-sm">{{ form.errors.otp }}</div>
      <button
        type="submit"
        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded w-full mt-2"
      >
        Verifikasi
      </button>
    </form>

    <div class="mt-4 text-sm text-gray-600">
      Waktu tersisa: <strong>{{ Math.floor(countdown / 60) }}:{{ String(countdown % 60).padStart(2, '0') }}</strong>
    </div>

    <button
      @click="resendOtp"
      :disabled="!canResend"
      class="mt-3 text-sm text-red-600 hover:underline disabled:opacity-50"
    >
      Kirim Ulang OTP
    </button>
  </div>
</template>
