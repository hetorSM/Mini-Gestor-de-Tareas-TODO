<div class="p-6 max-w-4xl mx-auto">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Mis Tareas</h1>
        <button wire:click="$set('showModal', true)" class="bg-blue-500 text-white px-4 py-2 rounded">
            Nueva Tarea
        </button>
    </div>

    <div class="flex gap-2 mb-4 flex-wrap">
        <button wire:click="$set('filter', 'all')" class="px-3 py-1 rounded {{ $filter == 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">Todas</button>
        <button wire:click="$set('filter', 'pending')" class="px-3 py-1 rounded {{ $filter == 'pending' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">Pendientes</button>
        <button wire:click="$set('filter', 'completed')" class="px-3 py-1 rounded {{ $filter == 'completed' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">Completadas</button>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar tareas..." class="border rounded px-3 py-1 ml-auto">
    </div>

    <div class="space-y-3">
        @foreach($tasks as $task)
            <div class="border rounded p-4 flex items-start justify-between" x-data="{ showDescription: false }">
                <div class="flex items-start gap-3 flex-1">
                    <input type="checkbox" wire:change="toggleCompleted({{ $task->id }})" @checked($task->completed) class="mt-1">

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg {{ $task->completed ? 'line-through text-gray-500' : '' }}">
                                {{ $task->title }}
                            </h3>
                            @if($task->category)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                    {{ $task->category->name }}
                                </span>
                            @endif
                        </div>

                        @if($task->description)
                            <button @click="showDescription = !showDescription" class="text-sm text-blue-500 mt-1">
                                <span x-show="!showDescription">Mostrar descripción</span>
                                <span x-show="showDescription">Ocultar descripción</span>
                            </button>
                            <p x-show="showDescription" x-transition class="text-gray-600 mt-2">
                                {{ $task->description }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <button wire:click="editTask({{ $task->id }})" class="text-blue-500 hover:text-blue-700">✏️</button>
                    <button onclick="confirm('¿Eliminar tarea?') || event.stopImmediatePropagation()" wire:click="deleteTask({{ $task->id }})" class="text-red-500 hover:text-red-700">🗑️</button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('showModal') }" x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="open = false" class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">{{ $editingTask ? 'Editar Tarea' : 'Nueva Tarea' }}</h2>

            <form wire:submit.prevent="{{ $editingTask ? 'updateTask' : 'createTask' }}">
                <div class="mb-4" x-data="{ title: @entangle('title'), error: '' }">
                    <label class="block mb-1">Título *</label>
                    <input type="text" x-model="title" @input="error = title.trim() ? '' : 'El título es obligatorio'" class="border rounded w-full px-3 py-2">
                    <template x-if="error">
                        <p class="text-red-500 text-sm mt-1" x-text="error"></p>
                    </template>
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Descripción</label>
                    <textarea wire:model="description" class="border rounded w-full px-3 py-2" rows="3"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Categoría</label>
                    <select wire:model="category_id" class="border rounded w-full px-3 py-2">
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
