<script lang="ts" setup>
import { getReservations, reserveHour } from '@/api/common-area/reservations'
import type { GetReservationRequest, Hour, ScheduleResponse } from '@/api/common-area/types'
import HoursList from '@/components/common-area/HoursList.vue'
import ModalConfirm from '@/components/ModalConfirm.vue'
import { AREAS, getAreaLabel } from '@/utils/area'
import { formatDate } from '@/utils/date'
import { onMounted, reactive, ref } from 'vue'

const confirmModal = ref<InstanceType<typeof ModalConfirm>>()

const form = reactive<Omit<GetReservationRequest, 'date'> & { date: string }>({
  area: 'padel',
  date: formatDate(new Date())
})

const schedule = reactive<ScheduleResponse>({
  hours: [],
})

const resultInfo = ref<{
  area: string
  date: string
}>()

const askConfirmation = async (hour: Hour) => {
  if (!confirmModal.value) return

  confirmModal.value.onConfirm = () => handleReserveHour(hour)
  confirmModal.value.show()
}

const handleGetReservations = async () => {
  const request = {
    area: form.area,
    date: new Date(form.date),
  }

  const [error, response] = await getReservations(request)

  if (error) {
    console.error(error)
    return
  }

  schedule.hours = response.hours

  resultInfo.value = {
    area: getAreaLabel(form.area),
    date: formatDate(new Date(form.date), '/', 'es'),
  }
}

const handleReserveHour = async (hour: Hour) => {
  await reserveHour({
    area: form.area,
    date: new Date(form.date),
    hour: hour.hour,
  })
  handleGetReservations()
}

onMounted(handleGetReservations)
</script>

<template>
  <div class="bg-gray-50 h-[100vh]">
    <div class="space-y-16 py-20">
      <div class="flex flex-col gap-6 items-center">
        <select class="w-50 border p-2" v-model="form.area">
          <option value="" disabled selected>Área Común</option>
          <option v-for="area in AREAS" :key="area.value" :value="area.value">
            {{ area.label }}
          </option>
        </select>

        <input
          type="date"
          class="w-50 border p-2"
          v-model="form.date"
          :min="formatDate(new Date())"
        />

        <button
          @click="handleGetReservations"
          class="p-3 bg-blue-500 rounded-md text-gray-50 cursor-pointer"
        >
          CONSULTAR
        </button>
      </div>
      <div class="space-y-6">
        <h2 v-if="resultInfo" class="text-lg max-w-lg mx-auto">Horas disponibles para {{ resultInfo?.area }} el {{ resultInfo?.date }}</h2>
        <HoursList class="mx-auto max-w-lg" :hours="schedule.hours" @reserve-hour="askConfirmation" />
      </div>
    </div>
    <ModalConfirm ref="confirmModal" />
  </div>
</template>
