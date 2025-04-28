@props([
    'title' => '',
    'content' => '',
    'id' => 'prompt-content'
])

<div class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-md p-6 mb-6">
    @if($title)
        <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-gray-100 font-sans" style="font-family: 'Inter', 'Poppins', sans-serif;">{{ $title }}</h2>
    @endif
    <pre id="{{ $id }}" class="whitespace-pre-wrap break-words text-gray-800 dark:text-gray-100 text-base font-mono" style="font-family: 'Inter', 'Poppins', 'Fira Mono', 'Menlo', 'Monaco', 'Consolas', monospace;">{{ $content }}</pre>
    <button id="copy-btn-{{ $id }}"
        class="absolute top-4 right-4 inline-flex items-center px-3 py-1.5 rounded-md bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
        type="button"
        aria-label="Copy prompt content"
    >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="9" y="9" width="13" height="13" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"/>
            <rect x="3" y="3" width="13" height="13" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"/>
        </svg>
        Copy
    </button>
    <span id="copy-confirm-{{ $id }}"
        class="absolute top-4 right-24 bg-green-600 text-white text-xs rounded px-2 py-1 opacity-0 pointer-events-none transition-opacity duration-200"
        aria-live="polite"
    >
        Copied!
    </span>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyBtn = document.getElementById('copy-btn-{{ $id }}');
            const promptContentElem = document.getElementById('{{ $id }}');
            const confirm = document.getElementById('copy-confirm-{{ $id }}');
            if (copyBtn && promptContentElem && confirm) {
                copyBtn.addEventListener('click', function () {
                    const text = promptContentElem.innerText;
                    navigator.clipboard.writeText(text).then(function () {
                        confirm.style.opacity = '1';
                        setTimeout(() => {
                            confirm.style.opacity = '0';
                        }, 1000);
                    });
                });
            }
        });
    </script>
</div>