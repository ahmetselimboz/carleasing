   <!-- Header - Açık renk -->
   <header class="header sticky top-0 z-20 bg-white/95 backdrop-blur-sm border-b border-slate-200">
       <div class="flex items-center justify-between gap-4 px-4 lg:px-6 py-3">
           <button type="button"
               class="sidebar-toggle lg:hidden p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-soft"
               data-tooltip="Menü" data-tooltip-position="bottom">
               <i class="ri-menu-line text-2xl"></i>
           </button>
           <div class="flex-1 flex items-center justify-between gap-4">
               {{-- <div class="relative flex-1 max-w-md">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" placeholder="Ara..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-[#37008a]/20 focus:border-[#37008a] transition-soft">
                </div> --}}
               <div class="flex items-center gap-2">
                   <form method="POST" action="{{ route('cache.clear') }}" class="inline-block"
                       onsubmit="return confirm('Önbellek temizlensin mi?');">
                       @csrf
                       <button type="submit"
                           class="p-2.5 cursor-pointer rounded-xl hover:bg-slate-100 text-slate-600 transition-soft"
                           data-tooltip="Önbelleği temizle" data-tooltip-position="bottom">
                           <i class="ri-refresh-line text-xl"></i>
                       </button>
                   </form>
                   <a href="{{ route('home') }}" target="_blank"
                       class="relative p-2.5 cursor-pointer rounded-xl hover:bg-slate-100 text-slate-600 transition-soft"
                       data-tooltip="Siteyi görüntüle" data-tooltip-position="bottom">
                       <i class="ri-computer-line text-xl"></i>
                   </a>
               </div>
               <div class="flex items-center gap-2">
                   <button type="button"
                       class="dark-mode-toggle p-2.5 cursor-pointer rounded-xl hover:bg-slate-100 text-slate-600 transition-soft"
                       data-tooltip="Tema değiştir" data-tooltip-position="bottom">
                       <i class="ri-moon-line text-xl"></i>
                   </button>
                  @php
                      $notificationTotal = data_get($adminNotifications ?? [], 'totalUnread', 0);
                      $notificationItems = data_get($adminNotifications ?? [], 'items', collect());
                      $notificationLinks = data_get($adminNotifications ?? [], 'links', []);
                  @endphp
                  <div class="relative" id="notification-dropdown">
                      <button type="button" data-dropdown-toggle="#notification-menu"
                          class="relative p-2.5 cursor-pointer rounded-xl hover:bg-slate-100 text-slate-600 transition-soft"
                          data-tooltip="Bildirimler" data-tooltip-position="bottom">
                          <i class="ri-notification-3-line text-xl"></i>
                          @if ($notificationTotal > 0)
                              <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                          @endif
                      </button>
                      <div id="notification-menu" data-dropdown
                          class="hidden absolute right-0 mt-2 w-[360px] max-w-[90vw] bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                          <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                              <p class="text-sm font-semibold text-slate-800">Bildirimler</p>
                              @if ($notificationTotal > 0)
                                  <span
                                      class="inline-flex items-center px-2 py-1 text-[11px] rounded-full bg-red-50 text-red-600 border border-red-100">
                                      {{ $notificationTotal }} yeni
                                  </span>
                              @endif
                          </div>
                          <div class="max-h-96 overflow-y-auto">
                              @forelse ($notificationItems as $notification)
                                  <a href="{{ $notification['url'] }}"
                                      class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100/80 last:border-b-0">
                                      <div class="flex items-start justify-between gap-3">
                                          <div>
                                              <p class="text-sm font-medium text-slate-800">{{ $notification['title'] }}</p>
                                              <p class="text-xs text-slate-600 mt-0.5">{{ $notification['description'] }}</p>
                                          </div>
                                          <span class="text-[11px] text-slate-400 whitespace-nowrap">
                                              {{ optional($notification['created_at'])->diffForHumans() }}
                                          </span>
                                      </div>
                                  </a>
                              @empty
                                  <p class="px-4 py-8 text-sm text-center text-slate-500">Yeni bildirim bulunmuyor.</p>
                              @endforelse
                          </div>
                          <div class="px-2 pt-2 border-t border-slate-100 grid grid-cols-1 gap-1">
                              <a href="{{ data_get($notificationLinks, 'rentalRequests') }}"
                                  class="text-xs px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-600">Kiralama taleplerini
                                  görüntüle</a>
                              <a href="{{ data_get($notificationLinks, 'messages') }}"
                                  class="text-xs px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-600">İletişim mesajlarını
                                  görüntüle</a>
                              <a href="{{ data_get($notificationLinks, 'weCallYou') }}"
                                  class="text-xs px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-600">Geri arama taleplerini
                                  görüntüle</a>
                          </div>
                      </div>
                  </div>
               </div>
           </div>
           <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
               <div class="hidden sm:block text-right">
                   <p class="text-sm font-medium text-slate-800">{{ auth()->user()->displayName() }}</p>
                   <p class="text-xs text-slate-500">
                       {{ auth()->user()->is_super_admin ? 'Panel' : ucfirst(auth()->user()->role) }}</p>
               </div>
               <div class="relative" id="user-dropdown">
                   <button data-dropdown-toggle="#user-menu"
                       class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition-soft">
                       <div
                           class="w-9 h-9 rounded-xl bg-[#37008a] flex items-center justify-center text-white font-medium">
                           <i class="ri-user-3-line"></i>
                       </div>
                       <i class="ri-arrow-down-s-line text-slate-400"></i>
                   </button>
                   <div id="user-menu" data-dropdown
                       class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                       <a href="#"
                           class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50 text-sm"><i
                               class="ri-user-line"></i> Profil</a>
                       <a href="{{ route('settings') }}"
                           class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50 text-sm"><i
                               class="ri-settings-3-line"></i> Ayarlar</a>
                       <hr class="my-2 border-slate-100">
                       <form method="POST" action="{{ route('logout') }}" class="block">
                           @csrf
                           <button type="submit"
                               class="flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 text-sm w-full text-left"><i
                                   class="ri-logout-box-r-line"></i> Çıkış</button>
                       </form>
                   </div>
               </div>
           </div>
       </div>
   </header>
