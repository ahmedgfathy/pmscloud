<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Control System - PMS Cloud</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Cairo:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Remove RTL styles */
        .select2-container--default .select2-selection--single {
            text-align: left;
        }

        /* Language-specific fonts */
        body {
            font-family: {{ app()->getLocale() == 'ar' ? 'Cairo' : 'Roboto' }}, sans-serif;
        }
        
        /* RTL adjustments */
        html[dir="rtl"] .select2-container--default .select2-selection--single {
            text-align: right;
        }
        
        /* Dropdown style */
        .lang-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .lang-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            min-width: 120px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border-radius: 0.375rem;
            overflow: hidden;
        }
        
        .lang-dropdown:hover .lang-dropdown-content {
            display: block;
        }

        .upload-container {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filepond--root {
            width: 100%;
            margin: 0;
        }

        .filepond--panel-root {
            background-color: transparent;
        }

        .filepond--drop-label {
            color: #6B7280;
            font-size: 1rem;
            text-align: center;
        }

        .filepond--label-action {
            text-decoration-color: #6366F1;
            color: #6366F1;
        }

        .filepond--drip {
            background-color: #818CF8;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left side -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-indigo-400 bg-clip-text text-transparent">PMS CLOUD</h1>
                    </div>
                </div>

                <!-- Center -->
                <div class="hidden sm:flex sm:items-center">
                    <div class="flex space-x-1 bg-gray-100 p-1 rounded-xl">
                        <a href="{{ route('dashboard') }}" 
                           class="px-4 py-2 text-sm font-medium rounded-lg {{ !isset($isSharedView) ? 'bg-white shadow text-gray-800' : 'text-gray-600 hover:text-gray-800 hover:bg-white/[0.5]' }}">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>{{ __('Documents') }}</span>
                            </div>
                        </a>
                        <a href="{{ route('shared') }}" 
                           class="px-4 py-2 text-sm font-medium rounded-lg {{ isset($isSharedView) ? 'bg-white shadow text-gray-800' : 'text-gray-600 hover:text-gray-800 hover:bg-white/[0.5]' }}">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316"/>
                                </svg>
                                <span>{{ __('Shared with me') }}</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2 bg-gray-100 px-4 py-1.5 rounded-full">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-medium">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" 
                                 x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Language Switcher -->
                    <div class="relative">
                        <button type="button" onclick="document.getElementById('lang-dropdown').classList.toggle('hidden')"
                                class="flex items-center space-x-1 px-3 py-1.5 text-gray-700 hover:text-gray-900 rounded-md hover:bg-gray-100">
                            <span class="text-sm font-medium">{{ strtoupper(app()->getLocale()) }}</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="lang-dropdown" class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                            <!-- Change to direct form submissions -->
                            <form action="{{ route('language.switch', 'en') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() == 'en' ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                    English
                                </button>
                            </form>
                            <form action="{{ route('language.switch', 'ar') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() == 'ar' ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                    العربية
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- View Toggle & Controls -->
        <div class="px-4 sm:px-0 mb-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('Your Files') }}</h2>
                <div class="flex items-center space-x-4">
                    <button id="gridView" class="text-indigo-600 hover:text-indigo-900">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5z"></path>
                            <path d="M11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                    <button id="listView" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="px-4 sm:px-0 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium mb-4">{{ __('Upload Files') }}</h2>
                <div class="upload-container border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-indigo-500 transition-colors">
                    <input type="file" class="filepond" name="files[]" multiple>
                </div>
            </div>
        </div>

        <!-- Files Grid -->
        <div class="px-4 sm:px-0">
            <div class="bg-white rounded-lg shadow">
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2 p-3">
                    @forelse ($files as $file)
                        <div class="group relative bg-white p-2 rounded-lg border hover:shadow-lg transition-shadow">
                            <!-- File Preview -->
                            <div class="aspect-w-1 aspect-h-1 mb-1 rounded-md overflow-hidden bg-gray-50 relative h-16">
                                @if(in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/gif']))
                                    <img src="{{ Storage::url($file->path) }}" alt="{{ $file->name }}" class="object-cover w-full h-full">
                                @else
                                    <div class="flex items-center justify-center h-full p-2">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- File Info -->
                            <div class="text-[10px]">
                                <h3 class="font-medium text-gray-900 truncate">{{ $file->name }}</h3>
                                <p class="text-gray-500">{{ $file->created_at->format('M d, Y') }}</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-0.5">
                                <a href="{{ Storage::url($file->path) }}" target="_blank" class="p-0.5 rounded-full bg-blue-100 hover:bg-blue-200 transition-colors">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                <button onclick="shareFile({{ $file->id }})" class="p-0.5 rounded-full bg-green-100 hover:bg-green-200 transition-colors">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteFile({{ $file->id }})" class="p-0.5 rounded-full bg-red-100 hover:bg-red-200 transition-colors">
                                    <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500">{{ __('No files uploaded yet') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $files->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- Share Modal -->
    <div id="shareModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Share Document</h3>
                    <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Team Member</label>
                    <div class="relative">
                        <select id="userSelect" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option></option>
                        </select>
                        <div class="mt-1 text-xs text-gray-500">Start typing name or email to search</div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <span class="mr-2 text-sm text-gray-700">Read</span>
                            <input type="checkbox" id="canRead" class="rounded border-gray-300 text-indigo-600" checked disabled>
                        </label>
                        <br>
                        <label class="inline-flex items-center">
                            <span class="mr-2 text-sm text-gray-700">Write</span>
                            <input type="checkbox" id="canWrite" class="rounded border-gray-300 text-indigo-600">
                        </label>
                        <br>
                        <label class="inline-flex items-center">
                            <span class="mr-2 text-sm text-gray-700">Delete</span>
                            <input type="checkbox" id="canDelete" class="rounded border-gray-300 text-indigo-600">
                        </label>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-end space-x-3">
                    <button onclick="submitShare()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Share
                    </button>
                    <button onclick="closeShareModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Initialize FilePond
        FilePond.registerPlugin(
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize
        );

        const pond = FilePond.create(document.querySelector('input[type="file"]'), {
            server: {
                process: {
                    url: '{{ route("upload") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    onload: null,
                    onerror: (response) => {
                        try {
                            const data = JSON.parse(response);
                            return data.message || 'Upload failed';
                        } catch (error) {
                            return 'Upload failed';
                        }
                    }
                },
                revert: null,
                restore: null,
                load: null,
                fetch: null
            },
            allowMultiple: true,
            maxFiles: 10,
            maxFileSize: '500MB',
            acceptedFileTypes: ['image/*', 'application/pdf', '.doc', '.docx', '.xls', '.xlsx'],
            labelIdle: 'Drag & Drop your files or <span class="filepond--label-action">Browse</span>',
            labelFileProcessing: 'Uploading...',
            labelFileProcessingComplete: 'Upload Complete',
            labelFileProcessingError: 'Upload Failed',
            labelTapToRetry: 'Click to retry',
            instantUpload: true,
            checkValidity: true,
            allowRevert: false,
            onprocessfiles: () => {
                window.location.reload();
            }
        });

        let currentFileId = null;

        // Share functionality
        function shareFile(fileId) {
            currentFileId = fileId;

            // Reset and initialize Select2
            if ($('#userSelect').hasClass('select2-hidden-accessible')) {
                $('#userSelect').select2('destroy');
            }

            $('#userSelect').select2({
                dropdownParent: $('#shareModal'),
                placeholder: 'Search for team member...',
                allowClear: true,
                ajax: {
                    url: '{{ route("users.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(user) {
                                return {
                                    id: user.id,
                                    text: user.name + ' (' + user.email + ')'
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                templateResult: formatUserOption
            });

            document.getElementById('shareModal').classList.remove('hidden');
        }

        function formatUserOption(user) {
            if (!user.id) return user.text;
            return $(`<div class="flex flex-col py-1">
                <div class="font-medium">${user.text.split(' (')[0]}</div>
                <div class="text-xs text-gray-500">${user.text.split(' (')[1].replace(')', '')}</div>
            </div>`);
        }

        function closeShareModal() {
            document.getElementById('shareModal').classList.add('hidden');
            currentFileId = null;
        }

        function submitShare() {
            const userId = $('#userSelect').val();
            if (!userId) {
                alert('Please select a team member to share with');
                return;
            }

            const permissions = {
                can_read: true,
                can_write: document.getElementById('canWrite').checked,
                can_delete: document.getElementById('canDelete').checked
            };

            fetch(`/files/${currentFileId}/share`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ user_id: userId, permissions })
            })
            .then(response => response.json())
            .then(data => {
                alert('File shared successfully');
                closeShareModal();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sharing file');
            });
        }

        // View toggle functionality
        document.getElementById('gridView').addEventListener('click', function() {
            this.classList.add('text-indigo-600');
            this.classList.remove('text-gray-400');
            document.getElementById('listView').classList.add('text-gray-400');
            document.getElementById('listView').classList.remove('text-indigo-600');
        });

        document.getElementById('listView').addEventListener('click', function() {
            this.classList.add('text-indigo-600');
            this.classList.remove('text-gray-400');
            document.getElementById('gridView').classList.add('text-gray-400');
            document.getElementById('gridView').classList.remove('text-indigo-600');
        });

        // Language switcher function
        function submitLangForm(lang) {
            event.preventDefault();
            const form = document.getElementById(`langForm${lang.charAt(0).toUpperCase() + lang.slice(1)}`);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ locale: lang })
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Close language dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('lang-dropdown');
            const button = event.target.closest('button');
            if (!button || !button.matches('[onclick*="lang-dropdown"]')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
