<script setup lang="ts">
import { ref } from 'vue'

const modal = ref<HTMLDialogElement>()

const onConfirm = ref<() => Promise<void>>()

const handleAction = async (fn?: () => Promise<void>) => {
  fn?.().then(() => {
    onConfirm.value = undefined
  })
  close()
}

const show = () => {
  modal.value?.showModal()
}

const close = () => {
  modal.value?.close()
}

defineExpose({ show, close, onConfirm })
</script>

<template>
  <dialog
    ref="modal"
    class="py-8 fixed top-[50%] space-y-14 left-[50%] translate-x-[-50%] translate-y-[-50%] rounded-md w-md bg-white shadow-lg"
  >
    <section class="text-center">
      <p class="text-lg font-semibold">Are you sure?</p>
    </section>
    <menu class="flex gap-10 justify-center">
      <button class="hover:bg-gray-100 rounded px-3 py-2 border cursor-pointer" @click="close">
        Cancel
      </button>
      <button
        class="px-3 py-2 bg-green-400 text-gray-50 rounded font-bold cursor-pointer hover:bg-green-500"
        @click="handleAction(onConfirm)"
      >
        Confirm
      </button>
    </menu>
  </dialog>
</template>
