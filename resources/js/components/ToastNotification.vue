<template>
  <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
    <transition-group name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="pointer-events-auto flex items-center justify-between p-4 rounded-xl shadow-lg border text-sm font-medium transition-all duration-300"
        :class="{
          'bg-white text-gray-900 border-gray-200': toast.type === 'info',
          'bg-black text-white border-black': toast.type === 'success',
          'bg-red-50 text-red-900 border-red-200': toast.type === 'error'
        }"
      >
        <div class="flex items-center gap-3">
          <span v-if="toast.type === 'success'" class="w-2 h-2 rounded-full bg-emerald-400"></span>
          <span v-else-if="toast.type === 'error'" class="w-2 h-2 rounded-full bg-red-500"></span>
          <span v-else class="w-2 h-2 rounded-full bg-blue-500"></span>
          <p class="text-sm">{{ toast.message }}</p>
        </div>
        <button
          @click="removeToast(toast.id)"
          class="ml-3 text-xs opacity-60 hover:opacity-100 transition-opacity p-1"
        >
          ✕
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
defineProps({
  toasts: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['remove']);

function removeToast(id) {
  emit('remove', id);
}
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateY(16px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
