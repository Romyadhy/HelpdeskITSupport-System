{{-- Show Ticket Modal --}}
<div x-show="showShowModal" x-cloak class="flex items-center justify-center fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">

    {{-- Overlay --}}
    <div x-show="showShowModal"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         @click="closeModals()"></div>

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

                {{-- Info Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-gray-700">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-layer-group text-teal-500"></i>
                            <span><strong>Category:</strong> <span x-text="showData.category || '-'"></span></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-map-marker-alt text-teal-500"></i>
                            <span><strong>Location:</strong> <span x-text="showData.location || '-'"></span></span>
                    </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-user text-teal-500"></i>
                                <span><strong>Created By:</strong> <span x-text="showData.user || 'Unknown'"></span></span>
                        </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar text-teal-500"></i>
                            <span><strong>Created At:</strong> <span x-text="showData.created_at"></span></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-flag text-teal-500"></i>
                            <span><strong>Priority:</strong>
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': showData.priority === 'Low',
                                                'bg-orange-100 text-orange-800': showData.priority === 'Medium',
                                                'bg-red-100 text-red-800': showData.priority === 'High'
                                            }"
                                            x-text="showData.priority"></span>
                                </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-stopwatch text-teal-500"></i>
                            <span><strong>Duration:</strong></span>
                                <template x-if="showData.duration_human">
                            <span>
                                <span class="text-gray-700" x-text="showData.duration_human"></span>
                                <small class="text-gray-500 text-xs ml-1" x-show="showData.duration_details" x-text="showData.duration_details"></small>
                            </span>
                            </template>
                            <template x-if="!showData.duration_human">
                                <span class="text-gray-500">-</span>
                            </template>
                    </div>
                </div>
 <!-- Solution Section (only if Closed) -->
                            <div x-show="showData.status === 'Closed' && showData.solution"
                                 class="bg-green-50 border-l-4 border-green-500 rounded-lg p-5">
                                <h5 class="text-lg font-semibold text-green-700 mb-2 flex items-center">
                                    <i class="fas fa-tools mr-2"></i> Solusi dari Masalah
                                </h5>
                                <p class="text-gray-700 leading-relaxed" x-text="showData.solution || 'Belum ada solusi yang tercatat.'"></p>
                            </div>

                            <!-- Assigned To -->
                            <div x-show="showData.assigned_to"
                                 class="bg-teal-50 border-l-4 border-teal-500 rounded-lg p-5">
                                <h5 class="text-lg font-semibold text-teal-700 mb-2 flex items-center">
                                    <i class="fas fa-user-cog mr-2"></i> Ditangani Oleh
                                </h5>
                                <p class="text-gray-700" x-text="showData.assigned_to || 'Unknown'"></p>
                            </div>


            </div>
        </div>

        {{-- Footer --}}
        {{-- <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse"> --}}
        {{--     <button @click="closeModals()" --}}
        {{--             class="mt-3 w-full inline-flex justify-center rounded-md border shadow-sm px-4 py-2 bg-white text-gray-700"> --}}
        {{--         Close --}}
        {{--     </button> --}}
        {{-- </div> --}}

    </div>
</div>
