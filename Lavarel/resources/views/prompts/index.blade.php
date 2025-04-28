{{--
    Prompts Index View
    - Dashboard for listing, filtering, importing, exporting, and managing prompts
    - Uses Alpine.js for UI state and interaction
    - All dynamic content is escaped for security
    - KISS: minimal markup where possible, comments for clarity
    - DEBUG: Dump $promptArray for validation (remove after check)
--}}
@php // dump($promptArray); @endphp
@extends('layouts.app')

@section('content')
<script>
/*
 * promptDashboard Alpine.js state & methods
 * - allPrompts, filteredPrompts: holds prompt data and filtered subset
 * - filters, sort, pagination: for search, sort, and paging
 * - importing, dragActive, importProgress, importError: for CSV/JSON import
 * - showBulkImportModal, showCreatePromptModal: modal visibility toggles
 * - isExportingCsv/Json: export state flags
 * - toast: feedback messages
 * - Core methods: filterPrompts, sortPrompts, paginatedPrompts, updatePagination, prev/nextPage, exportFile, uploadImportFile, etc.
 * - UI helpers: triggerFileInput, clearImportFileInfo, handleDragOver/Leave/Drop, showToast, deletePrompt
 * - All state & methods are referenced in markup or import/export logic
 */
function promptDashboard() {
    return {
        allPrompts: @json($promptArray),
        filteredPrompts: [],
        filters: { title: '', date: '' },
        sort: { column: 'created_at', direction: 'desc' },
        pagination: { page: 1, pageSize: 10, totalPages: 1, start: 0, end: 0 },
        importing: false,
        dragActive: false,
        importProgress: 0,
        importError: '',
        importedCount: 0,
        importFileName: '',
        importFileSize: '',
        showBulkImportModal: false,
        isExportingCsv: false,
        isExportingJson: false,
        toast: {
            success: { show: false, message: '' },
            error: { show: false, message: '' }
        },
        init() {
            this.filterPrompts();
            window.promptDashboard = this; // for toast triggers
        },
        filterPrompts() {
            let filtered = this.allPrompts.filter(p => {
                let titleMatch = !this.filters.title || p.title.toLowerCase().includes(this.filters.title.toLowerCase());
                let dateMatch = !this.filters.date || (p.created_at && p.created_at.startsWith(this.filters.date));
                return titleMatch && dateMatch;
            });
            this.filteredPrompts = filtered;
            this.sortPrompts();
            this.pagination.page = 1;
            this.updatePagination();
        },
        resetFilters() {
            this.filters.title = '';
            this.filters.date = '';
            this.filterPrompts();
        },
        sortBy(column) {
            if (this.sort.column === column) {
                this.sort.direction = this.sort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort.column = column;
                this.sort.direction = 'asc';
            }
            this.sortPrompts();
        },
        sortPrompts() {
            this.filteredPrompts.sort((a, b) => {
                let dir = this.sort.direction === 'asc' ? 1 : -1;
                if (a[this.sort.column] < b[this.sort.column]) return -1 * dir;
                if (a[this.sort.column] > b[this.sort.column]) return 1 * dir;
                return 0;
            });
            this.updatePagination();
        },
        paginatedPrompts() {
            const start = (this.pagination.page - 1) * this.pagination.pageSize;
            const end = Math.min(start + this.pagination.pageSize, this.filteredPrompts.length);
            this.pagination.start = start;
            this.pagination.end = end;
            return this.filteredPrompts.slice(start, end);
        },
        updatePagination() {
            this.pagination.totalPages = Math.max(1, Math.ceil(this.filteredPrompts.length / this.pagination.pageSize));
            this.pagination.start = (this.pagination.page - 1) * this.pagination.pageSize;
            this.pagination.end = Math.min(this.pagination.start + this.pagination.pageSize, this.filteredPrompts.length);
        },
        prevPage() {
            if (this.pagination.page > 1) {
                this.pagination.page--;
                this.updatePagination();
            }
        },
        nextPage() {
            if (this.pagination.page < this.pagination.totalPages) {
                this.pagination.page++;
                this.updatePagination();
            }
        },
        goToPage(page) {
            this.pagination.page = page;
            this.updatePagination();
        },
        dateFormat(dt) {
            if (!dt) return '';
            let d = new Date(dt);
            return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        },
        triggerFileInput() {
            this.$refs.importFileInput.click();
        },
        clearImportFileInfo() {
            this.importFileName = '';
            this.importFileSize = '';
        },
        handleDragOver() {
            this.dragActive = true;
        },
        handleDragLeave() {
            this.dragActive = false;
        },
        handleDrop(e) {
            this.dragActive = false;
            const files = e.dataTransfer.files;
            if (files.length) {
                this.importFileName = files[0].name;
                this.importFileSize = (files[0].size / 1024).toFixed(1) + ' KB';
                this.uploadImportFile(files[0]);
            }
        },
        handleFileSelect(e) {
            const files = e.target.files;
            if (files.length) {
                this.importFileName = files[0].name;
                this.importFileSize = (files[0].size / 1024).toFixed(1) + ' KB';
                this.uploadImportFile(files[0]);
            }
        },
        uploadImportFile(file) {
            this.importError = '';
            this.importing = true;
            this.importProgress = 0;
            this.importedCount = 0;
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['csv', 'json'].includes(ext)) {
                this.importError = 'Only CSV or JSON files are accepted.';
                this.importing = false;
                return;
            }
            let formData = new FormData();
            formData.append('import_file', file);
            let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || (this.allPrompts.length && this.allPrompts[0].csrf);

            fetch('/prompts/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData,
            }).then(async response => {
                let progress = 0;
                const progressStep = () => {
                    if (progress < 90) {
                        progress += 10 + Math.random() * 5;
                        this.importProgress = Math.min(progress, 90);
                        setTimeout(progressStep, 80);
                    }
                };
                progressStep();

                const data = await response.json().catch(() => ({}));
                this.importProgress = 100;
                setTimeout(() => {
                    this.importing = false;
                    if (response.ok && data.success) {
                        if (data.prompts && Array.isArray(data.prompts)) {
                            this.allPrompts = data.prompts.concat(this.allPrompts);
                            this.importedCount = data.imported || data.prompts.length;
                        }
                        this.filterPrompts();
                        this.showToast('success', data.message || 'Import successful.');
                        this.showBulkImportModal = false;
                        this.clearImportFileInfo();
                    } else {
                        this.importError = (data && data.message) ? data.message : 'Import failed.';
                        this.showToast('error', this.importError);
                    }
                }, 500);
            }).catch(err => {
                this.importing = false;
                this.importError = 'Upload failed. Please try again.';
                this.showToast('error', this.importError);
            });
        },
        showToast(type, message) {
            this.toast[type].show = true;
            this.toast[type].message = message;
            setTimeout(() => { this.toast[type].show = false; }, 3500);
        },
        deletePrompt(evt, prompt) {
            if (!confirm('Are you sure you want to delete this prompt?')) return;
            evt.target.closest('form').submit();
        },
        exportFile(type) {
            if (type === 'json') {
                console.log('[Alpine] exportFile called with type=json');
            }
            // UX: show loading, then download, then reset loading and show toast
            let loadingKey = type === 'csv' ? 'isExportingCsv' : 'isExportingJson';
            this[loadingKey] = true;
            setTimeout(() => {
                const data = this.filteredPrompts;
                if (!Array.isArray(data) || data.length === 0) {
                    // Empty data: download a file with only headers, or an empty array for JSON
                    if (type === 'csv') {
                        const headers = ['title', 'description', 'created_at'];
                        const csvContent = headers.join(',') + '\r\n';
                        this._downloadFile(csvContent, 'prompts.csv', 'text/csv');
                    } else if (type === 'json') {
                        this._downloadFile('[]', 'prompts.json', 'application/json');
                    }
                    this[loadingKey] = false;
                    this.showToast('success', 'Export complete!');
                    return;
                }
        
                if (type === 'csv') {
                    // Gather headers from keys, but only those that are common/expected
                    const headers = ['title', 'description', 'created_at'];
                    const escape = (v) => {
                        if (v === null || v === undefined) return '';
                        // Escape quotes/delimiters
                        return ('' + v).replace(/"/g, '""').replace(/\r?\n/g, ' ');
                    };
                    const csvRows = [
                        headers.join(','),
                        ...data.map(row =>
                            headers.map(h => `"${escape(row[h])}"`).join(',')
                        )
                    ];
                    const csvContent = csvRows.join('\r\n');
                    this._downloadFile(csvContent, 'prompts.csv', 'text/csv');
                } else if (type === 'json') {
                    // Only export relevant fields
                    const minimal = data.map(({ title, description, created_at }) => ({ title, description, created_at }));
                    this._downloadFile(JSON.stringify(minimal, null, 2), 'prompts.json', 'application/json');
                }
                this[loadingKey] = false;
                this.showToast('success', 'Export complete!');
            }, 350); // UX: short delay for loading state
        },
        _downloadFile(content, filename, mime) {
            // Helper to trigger file download (no dependencies, works on all browsers)
            const blob = new Blob([content], { type: mime });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => {
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 150);
        }
    }
}
</script>
<div
    x-data="promptDashboard()"
    x-init="init()"
    class="max-w-7xl mx-auto px-4 py-8 font-sans"
>
    <!-- Dashboard Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-bold mb-2 tracking-tight text-gray-100">Prompts</h1>
            <p class="text-gray-400 text-sm">Manage, search, and organize your prompts efficiently.</p>
        </div>
        <div class="flex flex-row space-x-4 items-center">
                <x-primary-button
                    type="button"
                    x-on:click="showCreatePromptModal = true"
                >
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create New Prompt
                    </span>
                </x-primary-button>
                <x-secondary-button
                    type="button"
                    x-on:click="showBulkImportModal = true"
                    class="bg-purple-600 hover:bg-purple-500 active:bg-purple-700 shadow-md shadow-purple-900/20 focus:ring-2 focus:ring-purple-400"
                >
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l3-3m-3 3l-3-3" />
                        </svg>
                        Bulk Import
                    </span>
                </x-secondary-button>
                <x-secondary-button
                    type="button"
                    x-on:click="console.log('[Alpine] Export CSV button clicked'); if (!isExportingCsv) { isExportingCsv = true; exportFile('csv'); }"
                    x-bind:disabled="isExportingCsv"
                    x-bind:aria-busy="isExportingCsv"
                    x-bind:aria-disabled="isExportingCsv"
                    class="border-2 border-neon-green hover:border-neon-green hover:shadow-neon-green bg-black/90 font-extrabold tracking-wider transition-all duration-150"
                >
                    <span class="inline-flex items-center gap-2" x-show="!isExportingCsv">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l3-3m-3 3l-3-3" />
                        </svg>
                        Export as CSV
                    </span>
                    <span class="inline-flex items-center gap-2" x-show="isExportingCsv">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Exporting...
                    </span>
                </x-secondary-button>
                <x-secondary-button
                    type="button"
                    x-on:click="console.log('[Alpine] Export JSON button clicked'); if (!isExportingJson) { isExportingJson = true; exportFile('json'); }"
                    x-bind:disabled="isExportingJson"
                    x-bind:aria-busy="isExportingJson"
                    x-bind:aria-disabled="isExportingJson"
                    class="border-2 border-neon-green hover:border-neon-green hover:shadow-neon-green bg-black/90 font-extrabold tracking-wider transition-all duration-150"
                >
                >
                    <span class="inline-flex items-center gap-2" x-show="!isExportingJson">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l3-3m-3 3l-3-3" />
                        </svg>
                        Export as JSON
                    </span>
                    <span class="inline-flex items-center gap-2" x-show="isExportingJson">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Exporting...
                    </span>
                </x-secondary-button>
        </div>
    </div>

    <!-- Toasts -->
    {{-- Modular Toasts --}}
    <x-toast
        type="success"
        x-bind:show="$toast['success']['show']"
        x-bind:message="$toast['success']['message']"
        extraClass=""
    />
    <x-toast
        type="error"
        x-bind:show="$toast['error']['show']"
        x-bind:message="$toast['error']['message']"
        extraClass=""
    />
    @if (session('success'))
        <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                window.promptDashboard.showToast('success', @json(session('success')));
            }, 100);
        });
        </script>
    @endif
    @if (session('error'))
        <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                window.promptDashboard.showToast('error', @json(session('error')));
            }, 100);
        });
        </script>
    @endif

    <!-- Filter/Search Controls -->
    <div class="flex flex-col md:flex-row md:items-end gap-4 mb-6">
        <div class="flex-1">
            <label class="block text-gray-300 text-sm font-bold mb-1 font-sans" for="searchTitle">Search Title</label>
            <x-text-input
                type="text"
                id="searchTitle"
                x-model="filters.title"
                x-on:input="filterPrompts"
                placeholder="Start typing to search title..."
                class="w-full px-4 py-3 rounded-lg border-2 border-gray-700 bg-gray-900 text-gray-100 font-sans font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500 transition-all duration-200"
                aria-label="Search Title"
                style="font-family: 'Inter', 'Poppins', sans-serif;"
            />
        </div>
        <div>
            <label class="block text-gray-300 text-sm font-bold mb-1 font-sans" for="filterDate">Date Created</label>
            <x-text-input
                type="date"
                id="filterDate"
                x-model="filters.date"
                x-on:input="filterPrompts"
                class="px-4 py-3 rounded-lg border-2 border-gray-700 bg-gray-900 text-gray-100 font-sans font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500 transition-all duration-200"
                aria-label="Date Created"
                style="font-family: 'Inter', 'Poppins', sans-serif;"
            />
        </div>
        <button
            type="button"
            x-on:click="resetFilters"
            class="mt-6 md:mt-0 px-5 py-3 rounded-lg bg-gray-700 hover:bg-gray-600 active:bg-gray-800 text-gray-100 font-bold font-sans transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-electric-blue"
        >Clear Filters</button>
    </div>

    <!-- Table -->
    <div class="shadow-xl rounded-2xl bg-gray-950 border border-gray-800 overflow-x-auto transition-all duration-300 hover:shadow-2xl hover:scale-[1.015]">
        <table class="min-w-full divide-y divide-gray-800">
            <thead>
                <tr>
                    <th class="py-3 px-4 text-left text-gray-300 font-semibold cursor-pointer select-none" x-on:click="sortBy('title')">
                        <div class="flex items-center gap-2">
                            Title
                            <svg x-show="sort.column === 'title'" x-bind:class="sort.direction === 'asc' ? '' : 'rotate-180'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </th>
                    <th class="py-3 px-4 text-left text-gray-300 font-semibold cursor-pointer select-none" x-on:click="sortBy('created_at')">
                        <div class="flex items-center gap-2">
                            Date Created
                            <svg x-show="sort.column === 'created_at'" x-bind:class="sort.direction === 'asc' ? '' : 'rotate-180'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </th>
                    <th class="py-3 px-4 text-left text-gray-300 font-semibold">Description</th>
                    <th class="py-3 px-4 text-center text-gray-300 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="prompt in paginatedPrompts()" x-bind:key="prompt.id">
                    <tr class="hover:bg-gray-900 hover:shadow-lg transition-all duration-200 group">
                        <td class="py-3 px-4 font-semibold text-gray-100 whitespace-pre-wrap break-words text-base sm:text-lg" style="word-break: break-word; font-family: 'Inter', 'Poppins', 'Segoe UI Emoji', 'Apple Color Emoji', sans-serif;">
                            <a x-bind:href="prompt.show_url" class="hover:underline hover:text-blue-400 transition" x-text="prompt.title"></a>
                        </td>
                        <td class="py-3 px-4 text-gray-400 whitespace-nowrap text-sm" x-text="dateFormat(prompt.created_at)"></td>
                        <td class="py-3 px-4 text-gray-300 whitespace-pre-wrap break-words max-w-xs text-base sm:text-lg" style="word-break: break-word; font-family: 'Inter', 'Poppins', 'Segoe UI Emoji', 'Apple Color Emoji', sans-serif;" x-text="prompt.description"></td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a
                                    x-bind:href="prompt.edit_url"
                                    class="inline-flex items-center px-3 py-2 rounded text-xs font-bold font-sans shadow transition-all duration-150 bg-blue-700 hover:bg-blue-600 hover:scale-105"
                                >
                                    Edit
                                </a>
                                <form x-bind:action="prompt.delete_url" method="POST" class="inline"
                                      x-on:submit.prevent="deletePrompt($event, prompt)">
                                    <input type="hidden" name="_token" x-bind:value="prompt.csrf">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-3 py-2 rounded text-xs font-bold font-sans shadow transition-all duration-150 bg-red-700 hover:bg-red-600 hover:scale-105"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="filteredPrompts.length === 0">
                    <td colspan="4" class="py-6 px-4 text-center text-gray-400">No prompts found.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Navigation -->
    <div class="flex flex-col md:flex-row md:justify-between items-center gap-4 mt-6">
        <div class="text-gray-400 text-sm">
            Showing
            <span class="font-semibold text-gray-100" x-text="pagination.start + 1"></span>
            to
            <span class="font-semibold text-gray-100" x-text="pagination.end"></span>
            of
            <span class="font-semibold text-gray-100" x-text="filteredPrompts.length"></span>
            prompts
        </div>
        <div class="flex gap-2">
            <button
                type="button"
                x-on:click="prevPage"
                x-bind:disabled="pagination.page === 1"
                class="px-5 py-3 min-h-[44px] rounded-xl border-2 border-neon-violet bg-gray-900 text-neon-violet font-bold text-lg font-sans
                hover:bg-neon-violet hover:text-white hover:shadow-neon-violet hover:scale-105 active:scale-95
                focus:outline-none focus:ring-2 focus:ring-neon-violet focus:ring-offset-2
                transition-all duration-200 ease-in-out disabled:opacity-50"
            >&larr; Prev</button>
            <template x-for="page in pagination.totalPages" x-bind:key="page">
                <button
                    x-on:click="goToPage(page)"
                    x-bind:class="{'bg-blue-600 text-white': pagination.page === page, 'bg-gray-800 text-gray-300 hover:bg-gray-700': pagination.page !== page}"
                    class="px-5 py-3 min-h-[44px] rounded-xl border-2 border-neon-violet bg-gray-900 text-neon-violet font-bold text-lg font-sans
                    hover:bg-neon-violet hover:text-white hover:shadow-neon-violet hover:scale-105 active:scale-95
                    focus:outline-none focus:ring-2 focus:ring-neon-violet focus:ring-offset-2
                    transition-all duration-200 ease-in-out"
                    x-text="page"
                ></button>
            </template>
            <button
                type="button"
                x-on:click="nextPage"
                x-bind:disabled="pagination.page === pagination.totalPages"
                class="px-5 py-3 min-h-[44px] rounded-xl border-2 border-neon-violet bg-gray-900 text-neon-violet font-bold text-lg font-sans
                hover:bg-neon-violet hover:text-white hover:shadow-neon-violet hover:scale-105 active:scale-95
                focus:outline-none focus:ring-2 focus:ring-neon-violet focus:ring-offset-2
                transition-all duration-200 ease-in-out disabled:opacity-50"
            >Next &rarr;</button>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <template x-if="showBulkImportModal">
        <div
            x-data="{
                close() { showBulkImportModal = false; clearImportFileInfo(); console.log('[DEBUG] Bulk Import Modal closed'); }
            }"
            x-init="console.log('[DEBUG] Bulk Import Modal opened')"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
            x-show="showBulkImportModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            tabindex="0"
            aria-modal="true"
            role="dialog"
        >
            <div class="bg-gray-900 w-full max-w-2xl rounded-3xl shadow-2xl border-2 border-neon-violet p-10 relative font-sans">
                <button
                    class="absolute top-4 right-5 text-gray-400 hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-neon-violet rounded-full transition"
                    x-on:click="close()"
                    aria-label="Close"
                >
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <h2 class="text-2xl font-bold text-neon-violet mb-6 font-sans" id="import-modal-title">Bulk Import Prompts</h2>
                <div
                    class="border-4 border-dashed rounded-2xl flex flex-col items-center justify-center py-16 px-6 cursor-pointer transition-all duration-300 ease-in-out relative outline-none w-full focus:ring-4 focus:ring-neon-violet/70 bg-gradient-to-br from-indigo-950 via-purple-950 to-gray-950"
                    :class="{
                        'border-neon-violet ring-4 ring-neon-violet/60 shadow-neon-violet animate-pulse scale-105 bg-gray-800/60': dragActive,
                        'border-gray-600 bg-gray-900': !dragActive
                    }"
                    tabindex="0"
                    role="button"
                    aria-describedby="import-modal-desc"
                    aria-label="Drag and drop CSV or JSON file here, or click to select"
                    x-on:click="triggerFileInput"
                    x-on:keydown.enter.prevent="triggerFileInput"
                    x-on:keydown.space.prevent="triggerFileInput"
                    x-on:dragover.prevent="handleDragOver"
                    x-on:dragleave.prevent="handleDragLeave"
                    x-on:dragend.prevent="handleDragLeave"
                    x-on:drop.prevent="handleDrop"
                    x-ref="dropzone"
                >
                    <input
                        type="file"
                        class="sr-only"
                        accept=".csv,.json,text/csv,application/json"
                        x-ref="importFileInput"
                        x-on:change="handleFileSelect"
                        tabindex="-1"
                        aria-hidden="true"
                    />
                    <svg class="w-16 h-16 text-neon-violet mb-3 pointer-events-none animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="text-gray-400 mb-2 text-lg font-bold pointer-events-none font-sans">Drag & drop CSV or JSON file here</span>
                    <span class="text-gray-500 text-sm pointer-events-none font-sans">or click to select file</span>
                    <div aria-live="polite" class="absolute left-2 right-2 top-2 text-xs text-blue-400 font-sans" x-show="importError" x-text="importError"></div>
                </div>
                <!-- File Info Feedback -->
                <template x-if="importFileName">
                    <div class="mt-6 w-full flex flex-col items-center gap-2">
                        <div class="text-sm text-blue-300 font-semibold font-sans flex items-center gap-3">
                            <svg class="w-5 h-5 text-neon-violet" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 002.829 2.828l6.586-6.586a2 2 0 00-2.829-2.828z" />
                            </svg>
                            <span>File: <span x-text="importFileName"></span> (<span x-text="importFileSize"></span>)</span>
                        </div>
                    </div>
                </template>
                <div class="mt-8 w-full">
                    <div x-show="importing" class="w-full bg-gray-800 rounded-full h-3 mb-3 overflow-hidden" aria-hidden="false">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-700"
                            x-bind:style="'width: ' + importProgress + '%'"
                            x-bind:aria-valuenow="importProgress"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            x-bind:aria-valuetext="'Import progress: ' + importProgress + '%'"
                            role="progressbar"
                        ></div>
                    </div>
                    <div x-show="importing" class="text-blue-400 text-base font-bold font-sans">Importing... <span x-text="importedCount > 0 ? '(' + importedCount + ' imported)' : ''"></span></div>
                </div>
                <div class="mt-10 text-xs text-gray-400 font-sans" id="import-modal-desc">
                    <b>Accepted formats:</b>
                    <ul class="list-disc pl-4 mt-1">
                        <li>
                            <b>CSV:</b> Must have headers <code>title,description</code>. Example:
                            <pre class="bg-gray-800 rounded px-2 py-1 mt-1 text-gray-300 whitespace-pre-wrap break-words font-sans">title,description
Prompt A,Description for A
Prompt B,Description for B</pre>
                        </li>
                        <li class="mt-2">
                            <b>JSON:</b> Array of objects, each with <code>title</code> and <code>description</code>. Example:
                            <pre class="bg-gray-800 rounded px-2 py-1 mt-1 text-gray-300 whitespace-pre-wrap break-words font-sans">[
  {"title": "Prompt A", "description": "Description for A"},
  {"title": "Prompt B", "description": "Description for B"}
]</pre>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </template>
</div>

    <!-- Create New Prompt Modal -->
    <x-modal
        name="create-prompt-modal"
        x-show="showCreatePromptModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
        style="display: none;"
    >
        <div class="bg-gray-900 w-full max-w-lg rounded-3xl shadow-2xl border-2 border-neon-violet p-10 relative font-sans">
            <button
                class="absolute top-4 right-5 text-gray-400 hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-neon-violet rounded-full transition"
                x-on:click="showCreatePromptModal = false"
                aria-label="Close"
            >
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h2 class="text-2xl font-bold text-neon-violet mb-6 font-sans">Create New Prompt</h2>
            <form
                @submit.prevent="
                    // DEBUG: Dummy handler – replace with actual submit logic as needed
                    showCreatePromptModal = false
                "
            >
                <div class="mb-5">
                    <label for="newPromptTitle" class="block text-gray-300 font-bold mb-2 font-sans">Title</label>
                    <input
                        id="newPromptTitle"
                        type="text"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-700 bg-gray-900 text-gray-100 font-sans font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500 transition-all duration-200"
                        placeholder="Prompt title"
                        required
                    />
                </div>
                <div class="mb-5">
                    <label for="newPromptDescription" class="block text-gray-300 font-bold mb-2 font-sans">Description</label>
                    <textarea
                        id="newPromptDescription"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-700 bg-gray-900 text-gray-100 font-sans font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500 transition-all duration-200"
                        placeholder="Prompt description"
                        rows="3"
                        required
                    ></textarea>
                </div>
                <div class="mb-8">
                    <label for="newPromptCategory" class="block text-gray-300 font-bold mb-2 font-sans">Category</label>
                    <input
                        id="newPromptCategory"
                        type="text"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-700 bg-gray-900 text-gray-100 font-sans font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500 transition-all duration-200"
                        placeholder="Prompt category"
                        required
                    />
                </div>
                <div class="flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="showCreatePromptModal = false">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Create
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

@endsection
