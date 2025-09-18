<div class="flex h-screen overflow-hidden">
  <div class="bg-[#001f54] pt-5 pb-4 flex flex-col fixed md:relative h-full transition-all duration-300 ease-in-out shadow-xl transform -translate-x-full md:transform-none md:translate-x-0" id="sidebar">
    <div class="flex items-center justify-between flex-shrink-0 px-4 mb-6 text-center">
      <h1 class="text-xl font-bold text-white items-center gap-2">
        <img id="sidebar-logo" src="layout/resources/images/logo.png" alt="">
        <img id="sonly" class="hidden w-full h-25" src="layout/resources/images/sonly.png" alt="">
      </h1>
    </div>

    <div class="flex-1 flex flex-col overflow-y-auto">
      <nav class="flex-1 px-2 space-y-1">
        <div class="px-4 py-2">
          <span class="text-xs font-semibold uppercase tracking-wider text-blue-300 sidebar-text">Main Menu</span>
        </div>
        <a href="" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="home" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Dashboard</span>
          </div>
        </a>

        <div class="px-4 py-2 mt-4">
          <span class="text-xs font-semibold uppercase tracking-wider text-blue-300 sidebar-text">Operations</span>
        </div>
        <div class="collapse group">
          <input type="checkbox" class="peer" />
          <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all peer-checked:bg-blue-600/50 text-white group">
            <div class="flex items-center">
              <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                <i data-lucide="calendar-check" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
              </div>
              <span class="ml-3 sidebar-text">Account Payable</span>
            </div>
            <i class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90 dropdown-icon" data-lucide="chevron-down"></i>
          </div>
          <div class="collapse-content pl-14 pr-4 py-1 space-y-1">       
            <a href="sub_module_2_payable.php" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white">
              <span class="flex items-center gap-2">
                <i data-lucide="bed" class="w-4 h-4 text-[#F7B32B]"></i>
              payable accounts
              </span>
            </a>
            
           <a href="sub_module_2_reject_payable.php" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white">
              <span class="flex items-center gap-2">
                <i data-lucide="bot" class="w-4 h-4 text-[#F7B32B]"></i>
               FOLLOW UP
              </span>
            </a>
            
          </div>
        </div>
        <div class="collapse group">
          <input type="checkbox" class="peer" />
          <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all peer-checked:bg-blue-600/50 text-white group">
            <div class="flex items-center">
              <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                <i data-lucide="users" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
              </div>
              <span class="ml-3 sidebar-text ">Disbursement</span>
            </div>
            <i class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90 dropdown-icon" data-lucide="chevron-down"></i>
          </div>
          <div class="collapse-content pl-14 pr-4 py-1 space-y-1">
          <a href="sub_module_4_disbursement_approval.php" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white">
              <span class="flex items-center gap-2">
                <i data-lucide="star" class="w-4 h-4 text-[#F7B32B]"></i>
              approval
              </span>
            </a>

            <a href="sub_module_4_disbursement_reports.php" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white">
              <span class="flex items-center gap-2">
                <i data-lucide="heart" class="w-4 h-4 text-[#F7B32B]"></i>
             Recepients
              </span>
            </a>

           
          </div>
        </div>
        

        <div class="px-4 py-2 mt-4">
          <span class="text-xs font-semibold uppercase tracking-wider text-blue-300 sidebar-text">Submodule</span>
        </div>
        <a href="" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="shopping-cart" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Submodule</span>
          </div>
        </a>
        <a href="/lar" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="award" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Submodule</span>
          </div>
        </a>
        <a href="/ias" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="package" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Submodule</span>
          </div>
        </a>
        <a href="/ecm" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="calendar-days" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Submodule</span>
          </div>
        </a>
        <a href="/hmp" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="megaphone" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Submodule</span>
          </div>
        </a>
        <a href="/hmm" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="brush-cleaning" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Submodule</span>
          </div>
        </a>
        <a href="/channel" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="share-2" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Submodule</span>
          </div>
        </a>
      </nav>
    </div>
  </div>
  <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
  <div class="flex flex-col flex-1 overflow-hidden">
    <header class="bg-base-100 shadow-sm z-10 border-b border-base-300 dark:border-gray-700" data-theme="light">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <button onclick="toggleSidebar()" class="btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105">
              <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
          </div>
          <div class="flex items-center gap-4">
            <div class="animate-fadeIn">
              <span id="philippineTime" class="font-medium max-md:text-sm"></span>
            </div>
            <div class="dropdown dropdown-end">
              <button id="notification-button" tabindex="0" class="btn btn-ghost btn-circle btn-sm relative">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
              </button>
              <ul tabindex="0" class="dropdown-content menu mt-3 z-[1] bg-[#001f54] rounded-lg shadow-xl overflow-hidden transform md:translate-x-0 sm:translate-x-1/2 sm:-translate-x-1/2">
                <li class="px-4 py-3 border-b flex justify-between items-center sticky top-0 bg-[#001f54] backdrop-blur-sm z-10">
                  <div class="flex items-center gap-2">
                    <i data-lucide="bell" class="w-5 h-5 text-blue-300"></i>
                    <span class="font-semibold text-white">Notifications</span>
                  </div>
                  <button class="text-blue-300 hover:text-white text-sm flex items-center gap-1">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Clear All</span>
                  </button>
                </li>
                <div class="max-h-96 overflow-y-auto">
                  <li class="px-4 py-3 hover:scale-105 transition-all">
                    <a class="bg-blue-700/50 flex items-start gap-3">
                      <div class="p-2 rounded-full bg-blue-600/30 text-blue-300">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-white"></i>
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-white flex items-center gap-2">
                          New Reservation
                          <span class="text-xs px-1.5 py-0.5 bg-blue-600 rounded-full">New</span>
                        </p>
                        <p class="text-sm text-white mt-1">John Doe booked Deluxe Suite for 3 nights</p>
                        <p class="text-xs text-white mt-2 flex items-center gap-1">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          10 minutes ago
                        </p>
                      </div>
                    </a>
                  </li>
                  <li class="px-4 py-3 hover:scale-105 transition-all">
                    <a class="bg-blue-700/50 flex items-start gap-3">
                      <div class="p-2 rounded-full bg-green-600/30 text-green-300">
                        <i data-lucide="check-circle" class="w-5 h-5 text-white"></i>
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-white">Check-in Complete</p>
                        <p class="text-sm text-white mt-1">Room 302 has been checked in</p>
                        <p class="text-xs text-white mt-2 flex items-center gap-1">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          1 hour ago
                        </p>
                      </div>
                    </a>
                  </li>
                  <li class="px-4 py-3 hover:scale-105 transition-all">
                    <a class="bg-red-600 flex items-start gap-3">
                      <div class="p-2 rounded-full bg-yellow-600/30 text-yellow-300">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-white flex items-center gap-2">
                          Maintenance Request
                          <span class="text-xs px-1.5 py-0.5 bg-yellow-600 rounded-full">Urgent</span>
                        </p>
                        <p class="text-sm text-white mt-1">AC not working in Room 215</p>
                        <p class="text-xs text-white mt-2 flex items-center gap-1">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          3 hours ago
                        </p>
                      </div>
                    </a>
                  </li>
                  <li class="px-4 py-3 hover:scale-105 transition-all">
                    <a class="bg-blue-700/50 flex items-start gap-3">
                      <div class="p-2 rounded-full bg-purple-600/30 text-purple-300">
                        <i data-lucide="message-circle" class="w-5 h-5 text-white"></i>
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-white">Guest Message</p>
                        <p class="text-sm text-white mt-1">Request for late checkout</p>
                        <p class="text-xs text-white mt-2 flex items-center gap-1">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          5 hours ago
                        </p>
                      </div>
                    </a>
                  </li>
                  <li class="px-4 py-3 hover:scale-105 transition-all">
                    <a class="bg-red-600 flex items-start gap-3">
                      <div class="p-2 rounded-full bg-red-600/30 text-red-300">
                        <i data-lucide="alert-octagon" class="w-5 h-5 text-white"></i>
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-white">Security Alert</p>
                        <p class="text-sm text-white mt-1">Unauthorized access attempt</p>
                        <p class="text-xs text-white mt-2 flex items-center gap-1">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          1 day ago
                        </p>
                      </div>
                    </a>
                  </li>
                  <li class="px-4 py-3 hover:scale-105 transition-all">
                    <a class="bg-blue-700/50 flex items-start gap-3">
                      <div class="p-2 rounded-full bg-blue-600/30 text-blue-300">
                        <i data-lucide="credit-card" class="w-5 h-5 text-white"></i>
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-white">Payment Received</p>
                        <p class="text-sm text-white mt-1">$450 for Room 204</p>
                        <p class="text-xs text-white mt-2 flex items-center gap-1">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          2 days ago
                        </p>
                      </div>
                    </a>
                  </li>
                </div>
                <li class="px-4 py-2 border-t sticky bottom-0 bg-[#001f54] backdrop-blur-sm">
                  <a class="text-center text-blue-300 hover:text-white text-sm flex items-center justify-center gap-1">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    <span>View All Notifications</span>
                  </a>
                </li>
              </ul>
            </div>
            <div class="dropdown dropdown-end">
              <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                <div class="w-8 rounded-full">
                  <img src="" alt="User Avatar" />
                </div>
              </label>
              <ul tabindex="0" class="dropdown-content menu mt-1 z-[100] w-52 bg-[#001f54] rounded-box shadow-xl">
                <li class="p-3 border-b">
                  <div class="bg-blue-700/50 rounded-md shadow-md flex items-center gap-3">
                    <div class="avatar">
                      <div class="w-10 rounded-full">
                        <img src="" alt="User Avatar" class="dark:brightness-90" />
                      </div>
                    </div>
                    <div>
                      <p class="font-medium text-white">John Smith</p>
                      <p class="text-xs text-white">Front Desk Manager</p>
                    </div>
                  </div>
                </li>
                <li>
                  <a class="flex items-center gap-2 px-4 py-2 text-white hover:bg-blue-700/50 transition-colors">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <span>Profile</span>
                  </a>
                </li>
                <li>
                  <a class="flex items-center gap-2 px-4 py-2 text-white hover:bg-blue-700/50 transition-colors">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>Settings</span>
                  </a>
                </li>
                <li>
                  <a class="flex items-center gap-2 px-4 py-2 text-white hover:bg-blue-700/50 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Sign out</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </header>