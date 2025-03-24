<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Agregar Clientes a una Audiencia</h2>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 p-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-4">
        <label class="block mb-1 font-semibold">Seleccionar Audiencia:</label>
        <select wire:model="audienceId" class="w-full border rounded p-2">
            <option value="">-- Selecciona una --</option>
            @foreach($audiences as $audience)
                <option value="{{ $audience->id }}">{{ $audience->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-semibold">Buscar Cliente:</label>
        <input type="text" wire:model.debounce.500ms="search" class="w-full border rounded p-2" placeholder="Nombre o email">
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-semibold">Selecciona Clientes:</label>
        <div class="max-h-64 overflow-y-auto border rounded p-2 space-y-1">
            @foreach($customers as $customer)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" wire:model="selectedCustomers" value="{{ $customer->id }}">
                    <span>{{ $customer->name }} - {{ $customer->email }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <button wire:click="addCustomersToAudience"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            @disabled(!$audienceId || empty($selectedCustomers))>
        Agregar a la Audiencia
    </button>
</div>
