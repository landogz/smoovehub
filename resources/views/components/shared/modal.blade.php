@props([
    'id',
    'title',
    'maxWidth' => 'max-w-[420px]',
])

<div
    id="{{ $id }}"
    class="sh-modal-bg"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
    aria-hidden="true"
>
    <div @class(['sh-modal', $maxWidth])>
        <div class="sh-modal-hd">
            <h3 id="{{ $id }}-title" class="sh-modal-title">{{ $title }}</h3>
            <button
                type="button"
                class="sh-modal-close"
                data-modal-close="{{ $id }}"
                aria-label="Close dialog"
            >
                ✕
            </button>
        </div>

        <div class="sh-modal-body">
            {{ $slot }}
        </div>
    </div>
</div>
