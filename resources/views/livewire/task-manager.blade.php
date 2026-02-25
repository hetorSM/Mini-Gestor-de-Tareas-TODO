<div class="p-6 max-w-4xl mx-auto">
    <!-- Toast Notification -->
    <div x-data="{ show: false, message: '' }"
         x-on:toast.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition.opacity.duration.300ms
         style="display: none;"
         class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg z-50">
        <span x-text="message"></span>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold dark:text-white">{{ __('Mis Tareas') }}</h1>
        <div class="flex items-center gap-4">
            <!-- Botón de Modo Oscuro -->
            <button x-data @click="document.documentElement.classList.toggle('dark')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white" title="Alternar Modo Oscuro">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
            <button wire:click="resetCreateModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors shadow">
                {{ __('+ Nueva Tarea') }}
            </button>
        </div>
    </div>

    <div class="flex gap-2 mb-4 flex-wrap">
        <button wire:click="$set('filter', 'all')" class="px-3 py-1 rounded transition-colors {{ $filter == 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}">{{ __('Todas') }}</button>
        <button wire:click="$set('filter', 'pending')" class="px-3 py-1 rounded transition-colors {{ $filter == 'pending' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}">{{ __('Pendientes') }}</button>
        <button wire:click="$set('filter', 'completed')" class="px-3 py-1 rounded transition-colors {{ $filter == 'completed' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}">{{ __('Completadas') }}</button>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Buscar tareas...') }}" class="border rounded px-3 py-1 ml-auto dark:bg-gray-800 dark:border-gray-600 dark:text-white">
    </div>

    <div class="space-y-3">
        @foreach($tasks as $task)
            <div class="border rounded p-4 flex items-start justify-between cursor-pointer hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors"
                 x-data="{ showDescription: false }"
                 @click="showDescription = !showDescription">
                <div class="flex items-start gap-3 flex-1">
                    <input type="checkbox" wire:click.stop="toggleCompleted({{ $task->id }})" @checked($task->completed) class="mt-1 w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-medium dark:text-white {{ $task->completed ? 'line-through text-gray-500 dark:text-gray-400' : '' }}">
                                {{ $task->title }}
                            </h3>
                            
                            @if($task->completed)
                                <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs px-2 py-1 rounded">
                                    {{ __('Completada') }}
                                </span>
                            @else
                                <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs px-2 py-1 rounded">
                                    {{ __('Pendiente') }}
                                </span>
                            @endif

                            @if($task->category)
                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded">
                                    {{ $task->category->name }}
                                </span>
                            @endif
                        </div>

                        @if($task->description)
                            <p x-show="showDescription" x-transition class="text-gray-600 dark:text-gray-300 mt-2 text-sm leading-relaxed" style="display: none;">
                                {{ $task->description }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <button wire:click.stop="editTask({{ $task->id }})" class="text-blue-500 hover:text-blue-700 p-1 transition-colors" title="{{ __('Editar') }}">✏️</button>
                    <button wire:click.stop="deleteTask({{ $task->id }})" wire:confirm="{{ __('¿Estás seguro de eliminar esta tarea?') }}" class="text-red-500 hover:text-red-700 p-1 transition-colors" title="{{ __('Eliminar') }}">🗑️</button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('showModal') }" x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-40 transition-opacity" style="display: none;">
        <div @click.away="open = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
            <h2 class="text-xl font-bold mb-4 dark:text-white">{{ $editingTask ? __('Editar Tarea') : __('Nueva Tarea') }}</h2>

            <form wire:submit.prevent="{{ $editingTask ? 'updateTask' : 'createTask' }}">
                <div class="mb-4" x-data="{ title: @entangle('title'), error: '' }">
                    <label class="block mb-1 dark:text-gray-300">Título *</label>
                    <input type="text" x-model="title" @input="error = title.trim() ? '' : 'El título es obligatorio'" class="border rounded w-full px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <template x-if="error">
                        <p class="text-red-500 text-sm mt-1" x-text="error"></p>
                    </template>
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if($editingTask)
                <div class="mb-4">
                    <label class="block mb-1">Descripción</label>
                    <textarea wire:model="description" class="border rounded w-full px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="3"></textarea>
                </div>
                @endif

                <div class="mb-6">
                    <label class="block mb-1 dark:text-gray-300">Categoría</label>
                    <select wire:model="category_id" class="border rounded w-full px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Sin categoría</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
