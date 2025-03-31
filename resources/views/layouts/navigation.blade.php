<nav x-data="{ mobileMenuOpen: false }" class="bg-white border-b border-gray-200 shadow-md fixed top-0 w-full z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center">
              <a href="/">
                  <img src="/img/perfil.png" alt="Logo" class="h-10">
              </a>
          </div>

          <!-- Desktop Links -->
          <div class="hidden md:flex space-x-6 items-center">
              <a href="/" class="text-gray-700 hover:text-blue-600 font-medium">Inicio</a>
              
              @auth
                  @if(Auth::user()->role_id == 1)
                      <a href="/chats" class="text-gray-700 hover:text-blue-600 font-medium">Inbox</a>
                      <a href="/customers/phase/1" class="text-gray-700 hover:text-blue-600 font-medium">Contactos</a>
                      <a href="/orders" class="text-gray-700 hover:text-blue-600 font-medium">Órdenes</a>
                      <a href="/products" class="text-gray-700 hover:text-blue-600 font-medium">Productos</a>
                      <a href="https://panel.smartchatapp.online/" class="text-gray-700 hover:text-blue-600 font-medium">Bot</a>
                      <a href="/actions" class="text-gray-700 hover:text-blue-600 font-medium">Acciones</a>
                      <!-- Dropdown -->
                      <div x-data="{ open: false }" class="relative">
                          <button @click="open = !open" class="text-gray-700 hover:text-blue-600 font-medium focus:outline-none">
                              Audiencias
                          </button>
                          <div x-show="open" @click.away="open = false"
                               class="absolute mt-2 w-40 bg-white border border-gray-200 shadow-lg rounded z-50">
                              <a href="/audiences/1/customers" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Magistrales</a>
                              <a href="/audiences/2/customers" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Terminados</a>
                              <a href="/audiences/3/customers" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Descuento</a>
                          </div>
                      </div>
                      <div x-data="{ open: false }" class="relative">
                          <button @click="open = !open" class="text-gray-700 hover:text-blue-600 font-medium focus:outline-none">
                              Reportes
                          </button>
                          <div x-show="open" @click.away="open = false"
                               class="absolute mt-2 w-40 bg-white border border-gray-200 shadow-lg rounded z-50">
                              <a href="/bi/newcustomers" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Clientes nuevos</a>
                              <a href="/bi/purchasefrequency" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Frecuencia de Compra</a>
                              <a href="/bi/averageTicket" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ticket Promedio</a>
                          </div>
                      </div>
                  @endif
              @endauth
          </div>

          <!-- Right side (auth + search) -->
          <div class="flex items-center space-x-4">
              @auth
                  <div x-data="{ open: false }" class="relative">
                      <button @click="open = !open" class="text-gray-700 hover:text-blue-600 font-medium focus:outline-none">
                          {{ Auth::user()->name }}
                      </button>
                      <div x-show="open" @click.away="open = false"
                           class="absolute mt-2 w-40 bg-white border border-gray-200 shadow-lg rounded z-50">
                          @if (Auth::user()->role_id == 1)
                              <a href="/config" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configuración</a>
                          @endif
                          <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                              Salir
                          </a>
                          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                              @csrf
                          </form>
                      </div>
                  </div>
              @else
                  <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium">Iniciar Sesión</a>
              @endauth

              @auth
              <!-- Search -->
              <form action="/customers" method="GET" class="hidden md:flex items-center space-x-2">
                  <input type="text" name="search" placeholder="Buscar..." value="{{ request()->search }}"
                      class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring focus:border-blue-300">
                  <button type="submit" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Ir</button>
              </form>
              @endauth
          </div>

          <!-- Mobile menu toggle -->
          <div class="md:hidden flex items-center">
              <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 focus:outline-none">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                  </svg>
              </button>
          </div>
      </div>
  </div>

  <!-- Mobile Menu -->
  <div x-show="mobileMenuOpen"
       class="md:hidden px-4 pb-4 bg-white shadow-md border-t border-gray-200">
      <a href="/" class="block py-2 text-gray-700 hover:text-blue-600">Inicio</a>
      @auth
          @if(Auth::user()->role_id == 1)
              <a href="/chats" class="block py-2 text-gray-700 hover:text-blue-600">Inbox</a>
              <a href="/customers/phase/1" class="block py-2 text-gray-700 hover:text-blue-600">Contactos</a>
              <a href="/orders" class="block py-2 text-gray-700 hover:text-blue-600">Órdenes</a>
              <a href="/products" class="block py-2 text-gray-700 hover:text-blue-600">Productos</a>
              <a href="/audiences/1/customers" class="block py-2 text-gray-700 hover:text-blue-600">Magistrales</a>
              <a href="/audiences/2/customers" class="block py-2 text-gray-700 hover:text-blue-600">Terminados</a>
              <a href="/audiences/3/customers" class="block py-2 text-gray-700 hover:text-blue-600">Descuento</a>
          @endif
      @endauth
  </div>
</nav>
