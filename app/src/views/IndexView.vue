<script lang="ts" setup>
import { ENV } from '@/utils/const'
import { formatDate } from '@/utils/date'
import { reactive } from 'vue'

const form = reactive({
  area: 'padel',
  date: formatDate(new Date()),
})

const schedule = reactive<{
  hours: Hour[]
}>({
  hours: [],
})

type Hour = {
  hour: number
  reserved: boolean
}

const consultReservations = async () => {
  const date = formatDate(new Date(form.date))
  const url = new URL(`${ENV.API_URL}/api/v1/common-area/schedule/${form.area}/${date}`)

  const data = await fetch(url.toString(), {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  }).then((response) => response.json())

  schedule.hours = await data.hours
}

const reserveHour = async (hour: Hour) => {
  console.log(hour)
}
</script>

<template>
  <div class="bg-gray-50 h-[100vh]">
    <div class="space-y-20 py-20">
      <div class="flex flex-col gap-6 items-center">
        <select class="w-50 border p-2" v-model="form.area">
          <option value="" disabled selected>Área Común</option>
          <option value="padel">Padel</option>
          <option value="gym">Gymnasio</option>
          <option value="pool">Piscina</option>
        </select>

        <input
          type="date"
          class="w-50 border p-2"
          v-model="form.date"
          :min="formatDate(new Date())"
        />

        <button
          @click="consultReservations"
          class="p-3 bg-blue-500 rounded-md text-gray-50 cursor-pointer"
        >
          CONSULTAR
        </button>
      </div>
      <div class="grid grid-cols-4 gap-4 max-w-sm mx-auto">
        <div
          v-for="hour in schedule.hours"
          :key="hour.hour"
          class="rounded p-6 text-center border"
          :class="{
            'bg-gray-400 text-gray-100 font-bold cursor-not-allowed': hour.reserved,
            'cursor-pointer': !hour.reserved,
          }"
          @click="reserveHour(hour)"
        >
          {{ hour.hour }}
        </div>
      </div>
    </div>
  </div>
</template>
