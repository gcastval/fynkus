<script lang="ts" setup>
import type { Hour } from '@/api/common-area/types'

interface Props {
  hours: Hour[]
}

defineProps<Props>()

const emit = defineEmits<{
  (e: 'reserve-hour', value: Hour): void
}>()

const handleReserveHour = (hour: Hour) => {
  emit('reserve-hour', hour)
}
</script>

<template>
  <div class="grid grid-cols-5 gap-6">
    <div
      v-for="hour in hours"
      :key="hour.hour"
      class="rounded p-6 border w-fit"
      :class="{
        'bg-gray-400 cursor-not-allowed': hour.reserved,
        'cursor-pointer': !hour.reserved,
      }"
      @click="() => handleReserveHour(hour)"
    >
      <span :class="{ 'line-through': hour.reserved }">
        {{ hour.hour.toString().padStart(2, '0') }}:00
      </span>
    </div>
  </div>
</template>
