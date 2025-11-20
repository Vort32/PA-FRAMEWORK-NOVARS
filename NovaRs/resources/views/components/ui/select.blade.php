@props([
    'label' => null,
    'model',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Choose an option',
    'autoSubmit' => false,
    'disabled' => false,
])

@php
    $componentId = 'ui-select-' . \Illuminate\Support\Str::uuid();
    $normalizedOptions = \Illuminate\Support\Collection::make($options)->mapWithKeys(function ($label, $value) {
        if (is_array($label) && array_key_exists('value', $label) && array_key_exists('label', $label)) {
            return [$label['value'] => $label['label']];
        }

        if (is_object($label) && isset($label->value, $label->label)) {
            return [$label->value => $label->label];
        }

        return [$value => $label];
    })->toArray();

    $currentValue = old($model, $selected);
    $currentLabel = $normalizedOptions[$currentValue] ?? $placeholder;
@endphp

<div {{ $attributes->class(['space-y-2', 'group'])->merge(['data-ui-select' => true, 'data-ui-select-autosubmit' => $autoSubmit ? 'true' : 'false', 'data-ui-select-disabled' => $disabled ? 'true' : 'false']) }}>
    @if ($label)
        <label for="{{ $componentId }}-button" class="text-sm font-medium text-[#2B6CB0] dark:text-blue-200">{{ $label }}</label>
    @endif

    <input type="hidden" name="{{ $model }}" value="{{ $currentValue }}" data-ui-select-input>

    <div class="relative">
        <button type="button" id="{{ $componentId }}-button" @if($disabled) disabled @endif class="flex w-full items-center justify-between rounded-xl border border-[#2B6CB0]/20 bg-white px-4 py-2.5 text-left text-sm text-slate-700 shadow-md transition focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/40 dark:border-blue-400/40 dark:bg-slate-900 dark:text-blue-100 {{ $disabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}" data-ui-select-trigger aria-haspopup="listbox" aria-expanded="false">
            <span data-ui-select-label>{{ $currentLabel }}</span>
            <i data-lucide="chevron-down" class="h-4 w-4 text-[#2B6CB0] transition group-data-[open=true]:rotate-180"></i>
        </button>

        <ul class="absolute z-50 mt-2 max-h-56 w-full origin-top transform overflow-auto rounded-xl border border-[#2B6CB0]/10 bg-white shadow-xl transition duration-150 ease-out focus:outline-none dark:border-blue-400/20 dark:bg-slate-900/95" data-ui-select-list role="listbox">
            @foreach ($normalizedOptions as $value => $optionLabel)
                <li>
                    <button type="button" data-ui-select-option data-value="{{ $value }}" class="flex w-full items-center justify-between gap-3 px-4 py-2 text-sm text-slate-700 transition hover:bg-[#2B6CB0]/10 hover:text-[#1E4E82] focus:outline-none dark:text-blue-100 dark:hover:bg-blue-500/20" role="option" aria-selected="{{ $currentValue == $value ? 'true' : 'false' }}">
                        <span>{{ $optionLabel }}</span>
                        <i data-lucide="check" data-ui-select-check class="h-4 w-4 text-[#2B6CB0] transition-opacity dark:text-blue-300 {{ $currentValue == $value ? '' : 'opacity-0' }}"></i>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const SELECT_SELECTOR = '[data-ui-select]';
                const OPEN_CLASS = 'data-ui-select-open';

                function closeAll(except = null) {
                    document.querySelectorAll(SELECT_SELECTOR).forEach(wrapper => {
                        if (wrapper === except) return;
                        setOpen(wrapper, false);
                    });
                }

                function setOpen(wrapper, open) {
                    const trigger = wrapper.querySelector('[data-ui-select-trigger]');
                    const list = wrapper.querySelector('[data-ui-select-list]');
                    if (!trigger || !list) return;

                    if (open) {
                        list.classList.remove('pointer-events-none', 'opacity-0', 'scale-95');
                        list.classList.add('pointer-events-auto', 'opacity-100', 'scale-100');
                        trigger.setAttribute('aria-expanded', 'true');
                        wrapper.dataset.open = 'true';
                    } else {
                        list.classList.remove('pointer-events-auto', 'opacity-100', 'scale-100');
                        list.classList.add('pointer-events-none', 'opacity-0', 'scale-95');
                        list.classList.remove('pointer-events-auto', 'opacity-100', 'scale-100');
                        trigger.setAttribute('aria-expanded', 'false');
                        delete wrapper.dataset.open;
                    }
                }

                function updateIcons(wrapper) {
                    if (window.lucide) {
                        window.lucide.createIcons({ root: wrapper });
                    }
                }

                function init(wrapper) {
                    const trigger = wrapper.querySelector('[data-ui-select-trigger]');
                    const list = wrapper.querySelector('[data-ui-select-list]');
                    const input = wrapper.querySelector('[data-ui-select-input]');
                    const labelSpan = wrapper.querySelector('[data-ui-select-label]');
                    if (!trigger || !list || !input || !labelSpan) return;

                    list.classList.add('pointer-events-none', 'opacity-0', 'scale-95');

                    trigger.addEventListener('click', (event) => {
                        event.stopPropagation();
                        if (wrapper.dataset.uiSelectDisabled === 'true') {
                            return;
                        }
                        const isOpen = !!wrapper.dataset.open;
                        closeAll(isOpen ? null : wrapper);
                        setOpen(wrapper, !isOpen);
                        updateIcons(wrapper);
                    });

                    list.querySelectorAll('[data-ui-select-option]').forEach(optionButton => {
                        optionButton.addEventListener('click', (event) => {
                            event.preventDefault();
                            if (wrapper.dataset.uiSelectDisabled === 'true') {
                                return;
                            }
                            const value = optionButton.getAttribute('data-value');
                            const optionLabel = optionButton.querySelector('span')?.textContent?.trim() ?? value;

                            input.value = value;
                            labelSpan.textContent = optionLabel;

                            list.querySelectorAll('[data-ui-select-option]').forEach(btn => btn.setAttribute('aria-selected', 'false'));
                            optionButton.setAttribute('aria-selected', 'true');

                            list.querySelectorAll('[data-ui-select-check]').forEach(icon => icon.classList.add('opacity-0'));
                            const activeIcon = optionButton.querySelector('[data-ui-select-check]');
                            if (activeIcon) {
                                activeIcon.classList.remove('opacity-0');
                            }

                            setOpen(wrapper, false);
                            updateIcons(wrapper);

                            if (wrapper.dataset.uiSelectAutosubmit === 'true') {
                                const form = wrapper.closest('form');
                                if (form) {
                                    form.submit();
                                }
                            }
                        });
                    });

                    updateIcons(wrapper);
                }

                    document.addEventListener('click', (event) => {
                        const target = event.target;
                    if (!target.closest(SELECT_SELECTOR)) {
                        closeAll();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeAll();
                    }
                });

                function initializeAll() {
                    document.querySelectorAll(SELECT_SELECTOR).forEach(wrapper => {
                        if (!wrapper.dataset.uiSelectReady) {
                            init(wrapper);
                            wrapper.dataset.uiSelectReady = 'true';
                        }
                    });
                }

                if (document.readyState !== 'loading') {
                    initializeAll();
                } else {
                    document.addEventListener('DOMContentLoaded', initializeAll);
                }

                document.addEventListener('ui-select:refresh', initializeAll);
            })();
        </script>
    @endpush
@endonce
