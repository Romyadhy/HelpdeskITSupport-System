{{-- Show Ticket Modal --}}
<div x-show="showShowModal" x-cloak class="flex items-center justify-center fixed inset-0 z-50 overflow-y-auto"
    role="dialog" aria-modal="true">

    {{-- Overlay --}}
    <div x-show="showShowModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals()">
    </div>

    {{-- Modal panel --}}
    <div x-show="showShowModal"
        class="inline-block align-bottom bg-white rounded-lg shadow-xl transform transition-all
                sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full max-h-[90vh] overflow-y-auto">

        {{-- HEADER --}}
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ticket Details</h3>
                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Loading Spinner --}}
            <div x-show="loading" class="text-center py-8">
                <i class="fas fa-circle-notch fa-spin text-4xl text-teal-500"></i>
                <p class="mt-2 text-gray-600">Loading ticket details...</p>
            </div>

            {{-- MAIN CONTENT --}}
            <div x-show="!loading" class="space-y-6">

                {{-- Title --}}
                <div class="flex flex-col md:flex-row md:justify-between pb-4 border-b">
                    <h4 class="text-3xl font-extrabold text-teal-600 uppercase" x-text="showData.title"></h4>

                    <span class="px-4 py-1.5 text-sm font-semibold rounded-full shadow-sm"
                        :class="{
                            'bg-blue-100 text-blue-800': showData.status === 'Open',
                            'bg-yellow-100 text-yellow-800': showData.status === 'In Progress',
                            'bg-green-100 text-green-800': showData.status === 'Closed'
                        }"
                        x-text="showData.status">
                    </span>
                </div>

                {{-- Description --}}
                <div class="bg-gray-50 border rounded-lg p-4">
                    <h5 class="text-lg font-semibold text-gray-800 mb-2">
                        <i class="fas fa-file-alt text-teal-500 mr-2"></i>Deskripsi Masalah:
                    </h5>
                    <p class="text-gray-700 leading-relaxed" x-text="showData.description"></p>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700">

                    <!-- Left Column -->
                    <div class="space-y-3">

                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <i class="fas fa-layer-group text-teal-500 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Category</p>
                                <p class="font-semibold text-gray-800" x-text="showData.category || '-'">
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <i class="fas fa-map-marker-alt text-teal-500 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Location</p>
                                <p class="font-semibold text-gray-800" x-text="showData.location || '-'">
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <i class="fas fa-user text-teal-500 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Created By</p>
                                <p class="font-semibold text-gray-800" x-text="showData.user || 'Unknown'"></p>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column -->
                    <div class="space-y-3">

                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <i class="fas fa-calendar text-teal-500 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Created At</p>
                                <p class="font-semibold text-gray-800" x-text="showData.created_at || '-'"></p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-flag text-teal-500 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Priority</p>
                                    <p class="font-semibold text-gray-800" x-text="showData.priority || '-'"></p>
                                </div>
                            </div>

                            <span class="px-2 py-0.5 rounded text-xs font-medium"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': showData.priority === 'Low',
                                    'bg-orange-100 text-orange-800': showData.priority === 'Medium',
                                    'bg-red-100 text-red-800': showData.priority === 'High'
                                }">
                                <span x-text="showData.priority"></span>
                            </span>
                        </div>

                        <!-- Durasi-->
                        <div class="border border-gray-200 rounded-lg p-3 bg-white shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                                    <i class="fas fa-stopwatch text-teal-500"></i>
                                    Informasi Durasi
                                </h5>

                                <span class="text-sm font-bold text-teal-600"
                                    x-text="showData.total_duration || '-'"></span>
                            </div>

                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">⏳ Menunggu</span>
                                    <span class="font-semibold text-gray-800"
                                        x-text="showData.waiting_duration || '-'"></span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-500">🔧 Pengerjaan</span>
                                    <span class="font-semibold text-gray-800"
                                        x-text="showData.progress_duration || '-'"></span>
                                </div>

                                <div class="pt-1 border-t flex justify-between">
                                    <span class="font-semibold text-gray-700">✅ Total</span>
                                    <span class="font-bold text-teal-600"
                                        x-text="showData.total_duration || '-'"></span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Solution Section (only if Closed) -->
                <div x-show="showData.status === 'Closed' && showData.solution"
                    class="bg-green-50 border-l-4 border-green-500 rounded-lg p-5">
                    <h5 class="text-lg font-semibold text-green-700 mb-2 flex items-center">
                        <i class="fas fa-tools mr-2"></i> Solusi dari Masalah
                    </h5>
                    <p class="text-gray-700 leading-relaxed"
                        x-text="showData.solution || 'Belum ada solusi yang tercatat.'">
                    </p>
                    <template x-if="showData.solution_image_url">
                        <img :src="showData.solution_image_url"
                            class="mt-3 rounded-lg max-h-60 cursor-pointer shadow"
                            @click="window.open(showData.solution_image_url, '_blank')">
                    </template>
                </div>

                <!-- Assigned To -->
                <div x-show="showData.assigned_to" class="bg-teal-50 border-l-4 border-teal-500 rounded-lg p-5">
                    <h5 class="text-lg font-semibold text-teal-700 mb-2 flex items-center">
                        <i class="fas fa-user-cog mr-2"></i> Ditangani Oleh
                    </h5>
                    <p class="text-gray-700" x-text="showData.assigned_to || 'Unknown'"></p>
                </div>

                <!-- Notes Section -->
                <div x-show="showData.notes && showData.notes.length"
                    class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-3 space-y-2">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-teal-500"></i>
                            Catatan Admin
                        </h5>
                    </div>

                    <!-- List Notes -->
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                        <template x-for="note in showData.notes" :key="note.id">
                            <div class="border border-gray-200 rounded-md px-2 py-1.5 bg-white">
                                <p class="text-xs text-gray-700 leading-relaxed" x-text="note.note"></p>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-[10px] text-gray-500">
                                        <i class="fas fa-user-shield mr-1"></i>
                                        <span x-text="note.author"></span>
                                    </span>
                                    <span class="text-[10px] text-gray-400" x-text="note.created_at"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>