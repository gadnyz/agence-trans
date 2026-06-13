<?= $this->extend('web/layouts/super_admin') ?>

<?= $this->section('content') ?>

<?php
// Valeurs de sécurité si les variables ne sont pas passées par le contrôleur
$user = session()->get('user') ?? [];
$displayName = trim((string) (($user['prenom'] ?? '') ?: ($user['username'] ?? 'Admin')));
$dateAujourdhui = date('F j, Y'); // Format: January 10, 2024
?>

<div class="space-y-lg">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Operations Overview</h2>
            <p class="text-body-lg text-outline">Real-time status of your transportation network for <?= esc($dateAujourdhui) ?></p>
        </div>
        <div class="flex gap-sm">
            <button class="flex items-center gap-sm px-md py-sm bg-surface-container-lowest border border-outline-variant rounded-lg font-label-lg hover:bg-surface-container-high transition-all">
                <span class="material-symbols-outlined text-[20px]">filter_list</span>
                Filter
            </button>
            <button class="flex items-center gap-sm px-md py-sm bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[20px]">share</span>
                Export Data
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
        <div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-md">
                <div class="p-sm bg-primary-fixed text-on-primary-fixed rounded-lg">
                    <span class="material-symbols-outlined">book_online</span>
                </div>
                <span class="text-label-md text-primary font-bold bg-primary-fixed px-sm py-xs rounded">+12%</span>
            </div>
            <h3 class="text-label-lg text-outline">Total Reservations</h3>
            <div class="flex items-baseline gap-sm">
                <p class="text-headline-md font-bold">$24,200</p>
            </div>
            <div class="mt-md h-12 w-full overflow-hidden opacity-50 group-hover:opacity-100 transition-opacity">
                <svg class="w-full h-full text-primary" preserveaspectratio="none" viewbox="0 0 100 20">
                    <path d="M0 15 Q 10 5, 20 12 T 40 8 T 60 14 T 80 5 T 100 10" fill="none" stroke="currentColor" stroke-width="2" vector-effect="non-scaling-stroke"></path>
                </svg>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-md">
                <div class="p-sm bg-secondary-fixed text-on-secondary-fixed rounded-lg">
                    <span class="material-symbols-outlined">directions_bus</span>
                </div>
                <span class="text-label-md text-secondary font-bold bg-secondary-fixed px-sm py-xs rounded">Live</span>
            </div>
            <h3 class="text-label-lg text-outline">Buses in Transit</h3>
            <div class="flex items-baseline gap-sm">
                <p class="text-headline-md font-bold">85</p>
                <span class="text-label-sm text-outline">/ 112 Total</span>
            </div>
            <div class="mt-md h-2 w-full bg-surface-container rounded-full overflow-hidden">
                <div class="h-full bg-primary" style="width: 75%"></div>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-md">
                <div class="p-sm bg-tertiary-fixed text-on-tertiary-fixed rounded-lg">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <span class="text-label-md text-on-tertiary-fixed-variant font-bold bg-tertiary-fixed px-sm py-xs rounded">+8.4%</span>
            </div>
            <h3 class="text-label-lg text-outline">Monthly Revenue</h3>
            <div class="flex items-baseline gap-sm">
                <p class="text-headline-md font-bold">$65,791</p>
            </div>
            <div class="mt-md h-12 w-full overflow-hidden opacity-50 group-hover:opacity-100 transition-opacity">
                <svg class="w-full h-full text-secondary" preserveaspectratio="none" viewbox="0 0 100 20">
                    <path d="M0 18 Q 20 18, 40 12 T 80 5 T 100 2" fill="none" stroke="currentColor" stroke-width="2" vector-effect="non-scaling-stroke"></path>
                </svg>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-md">
                <div class="p-sm bg-error-container text-on-error-container rounded-lg">
                    <span class="material-symbols-outlined">speed</span>
                </div>
                <span class="text-label-md text-error font-bold bg-error-container px-sm py-xs rounded">High</span>
            </div>
            <h3 class="text-label-lg text-outline">Driver Efficiency</h3>
            <div class="flex items-baseline gap-sm">
                <p class="text-headline-md font-bold">92%</p>
            </div>
            <div class="mt-md flex gap-xs">
                <div class="h-4 w-full bg-primary rounded-xs"></div>
                <div class="h-4 w-full bg-primary rounded-xs"></div>
                <div class="h-4 w-full bg-primary rounded-xs"></div>
                <div class="h-4 w-full bg-surface-container-high rounded-xs"></div>
            </div>
        </div>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
        <div class="flex justify-between items-center mb-xl">
            <div>
                <h3 class="font-title-lg text-title-lg text-on-surface">Demand & Revenue Analysis</h3>
                <p class="text-body-md text-outline">Comparing fleet utilization vs booking revenue over the last 30 days</p>
            </div>
            <div class="flex items-center gap-md">
                <div class="flex items-center gap-sm">
                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                    <span class="text-label-md text-outline">Demand</span>
                </div>
                <div class="flex items-center gap-sm">
                    <span class="w-3 h-3 rounded-full bg-secondary-container"></span>
                    <span class="text-label-md text-outline">Revenue</span>
                </div>
                <select class="bg-surface-container-low border-none rounded-lg text-label-lg px-md py-sm focus:ring-0">
                    <option>Last 30 Days</option>
                    <option>Last 6 Months</option>
                </select>
            </div>
        </div>
        
        <div class="h-72 w-full flex items-end justify-between gap-sm pt-md">
            <div class="flex-1 h-[40%] bg-primary rounded-t-lg relative group"><div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-on-surface text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">4.2k</div></div>
            <div class="flex-1 h-[65%] bg-primary rounded-t-lg relative group"><div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-on-surface text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">6.1k</div></div>
            <div class="flex-1 h-[55%] bg-primary rounded-t-lg relative group"><div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-on-surface text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">5.5k</div></div>
            <div class="flex-1 h-[85%] bg-primary rounded-t-lg relative group"><div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-on-surface text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">8.4k</div></div>
            <div class="flex-1 h-[45%] bg-primary rounded-t-lg relative group"><div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-on-surface text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">4.8k</div></div>
            <div class="flex-1 h-[70%] bg-primary rounded-t-lg relative group"><div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-on-surface text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">6.8k</div></div>
            <div class="flex-1 h-[95%] bg-primary rounded-t-lg relative group"><div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-on-surface text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">9.1k</div></div>
            <div class="flex-1 h-[60%] bg-primary rounded-t-lg relative group"></div>
            <div class="flex-1 h-[50%] bg-primary rounded-t-lg relative group"></div>
            <div class="flex-1 h-[80%] bg-primary rounded-t-lg relative group"></div>
            <div class="flex-1 h-[40%] bg-primary rounded-t-lg relative group"></div>
            <div class="flex-1 h-[65%] bg-primary rounded-t-lg relative group"></div>
        </div>
        <div class="flex justify-between mt-md px-sm text-label-md text-outline">
            <span>Jan 1</span><span>Jan 7</span><span>Jan 14</span><span>Jan 21</span><span>Jan 28</span><span>Feb 1</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-gutter pb-xl">
        
        <div class="xl:col-span-1 bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
            <div class="flex justify-between items-center mb-lg">
                <h3 class="font-title-md text-title-md text-on-surface">Active Fleet Distribution</h3>
                <button class="text-primary font-label-md hover:underline">Details</button>
            </div>
            <div class="relative h-48 flex items-center justify-center">
                <svg class="w-40 h-40 transform -rotate-90" viewbox="0 0 36 36">
                    <circle cx="18" cy="18" fill="transparent" r="16" stroke="#edeef0" stroke-width="4"></circle>
                    <circle cx="18" cy="18" fill="transparent" r="16" stroke="#1d4ed8" stroke-dasharray="75, 100" stroke-linecap="round" stroke-width="4"></circle>
                    <circle cx="18" cy="18" fill="transparent" r="16" stroke="#515f74" stroke-dasharray="15, 100" stroke-dashoffset="-75" stroke-linecap="round" stroke-width="4"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <p class="text-headline-md font-bold">112</p>
                    <p class="text-label-sm text-outline">Total Assets</p>
                </div>
            </div>
            <div class="mt-md space-y-sm">
                <div class="flex justify-between items-center p-sm bg-surface-container rounded-lg">
                    <div class="flex items-center gap-sm">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span class="text-label-lg">Regional Routes</span>
                    </div>
                    <span class="font-bold">75%</span>
                </div>
                <div class="flex justify-between items-center p-sm bg-surface-container rounded-lg">
                    <div class="flex items-center gap-sm">
                        <span class="w-2 h-2 rounded-full bg-secondary"></span>
                        <span class="text-label-lg">Long Haul</span>
                    </div>
                    <span class="font-bold">15%</span>
                </div>
                <div class="flex justify-between items-center p-sm bg-surface-container rounded-lg">
                    <div class="flex items-center gap-sm">
                        <span class="w-2 h-2 rounded-full bg-outline-variant"></span>
                        <span class="text-label-lg">Maintenance</span>
                    </div>
                    <span class="font-bold">10%</span>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
            <div class="flex justify-between items-center mb-lg">
                <h3 class="font-title-md text-title-md text-on-surface">Recent Reservations</h3>
                <button class="text-primary font-label-md hover:underline">View All</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant">
                        <tr>
                            <th class="py-sm px-md text-label-md text-outline">ID</th>
                            <th class="py-sm px-md text-label-md text-outline">Route / Trip</th>
                            <th class="py-sm px-md text-label-md text-outline">Client</th>
                            <th class="py-sm px-md text-label-md text-outline">Amount</th>
                            <th class="py-sm px-md text-label-md text-outline text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        <tr class="hover:bg-surface-container-low transition-colors cursor-pointer">
                            <td class="py-md px-md text-label-lg font-bold">#RS-7821</td>
                            <td class="py-md px-md">
                                <div class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-[18px] text-outline">route</span>
                                    <span class="text-body-md">SF → LA Express</span>
                                </div>
                            </td>
                            <td class="py-md px-md">
                                <div class="flex items-center gap-sm">
                                    <div class="w-6 h-6 rounded-full bg-secondary-fixed text-[10px] flex items-center justify-center font-bold">JD</div>
                                    <span class="text-body-md">John Doe</span>
                                </div>
                            </td>
                            <td class="py-md px-md text-body-md font-medium">$450.00</td>
                            <td class="py-md px-md text-right">
                                <span class="px-sm py-xs bg-primary-fixed text-on-primary-fixed-variant text-label-sm rounded-full">Confirmed</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors cursor-pointer">
                            <td class="py-md px-md text-label-lg font-bold">#RS-7819</td>
                            <td class="py-md px-md">
                                <div class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-[18px] text-outline">route</span>
                                    <span class="text-body-md">NYC → BOS Shuttler</span>
                                </div>
                            </td>
                            <td class="py-md px-md">
                                <div class="flex items-center gap-sm">
                                    <div class="w-6 h-6 rounded-full bg-primary-fixed text-[10px] flex items-center justify-center font-bold">SA</div>
                                    <span class="text-body-md">Sarah Alves</span>
                                </div>
                            </td>
                            <td class="py-md px-md text-body-md font-medium">$290.00</td>
                            <td class="py-md px-md text-right">
                                <span class="px-sm py-xs bg-tertiary-fixed text-on-tertiary-fixed-variant text-label-sm rounded-full">Pending</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors cursor-pointer">
                            <td class="py-md px-md text-label-lg font-bold">#RS-7815</td>
                            <td class="py-md px-md">
                                <div class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-[18px] text-outline">route</span>
                                    <span class="text-body-md">Miami → Orlando</span>
                                </div>
                            </td>
                            <td class="py-md px-md">
                                <div class="flex items-center gap-sm">
                                    <div class="w-6 h-6 rounded-full bg-surface-container-highest text-[10px] flex items-center justify-center font-bold">MB</div>
                                    <span class="text-body-md">Mike Brown</span>
                                </div>
                            </td>
                            <td class="py-md px-md text-body-md font-medium">$125.50</td>
                            <td class="py-md px-md text-right">
                                <span class="px-sm py-xs bg-error-container text-on-error-container text-label-sm rounded-full">Cancelled</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors cursor-pointer">
                            <td class="py-md px-md text-label-lg font-bold">#RS-7812</td>
                            <td class="py-md px-md">
                                <div class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-[18px] text-outline">route</span>
                                    <span class="text-body-md">Chicago Local</span>
                                </div>
                            </td>
                            <td class="py-md px-md">
                                <div class="flex items-center gap-sm">
                                    <div class="w-6 h-6 rounded-full bg-secondary-container text-[10px] flex items-center justify-center font-bold">EL</div>
                                    <span class="text-body-md">Emma Lee</span>
                                </div>
                            </td>
                            <td class="py-md px-md text-body-md font-medium">$85.00</td>
                            <td class="py-md px-md text-right">
                                <span class="px-sm py-xs bg-primary-fixed text-on-primary-fixed-variant text-label-sm rounded-full">Confirmed</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<button class="fixed bottom-lg right-lg w-14 h-14 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-30 group">
    <span class="material-symbols-outlined text-[24px]">add</span>
    <span class="absolute right-full mr-md px-md py-sm bg-inverse-surface text-inverse-on-surface text-label-lg rounded-lg opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity pointer-events-none shadow-xl">New Reservation</span>
</button>

<?= $this->endSection() ?>