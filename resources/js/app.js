import './bootstrap';
import 'leaflet/dist/leaflet.css';

import Alpine from 'alpinejs';
import Cropper from 'cropperjs';
import L from 'leaflet';
import Sortable from 'sortablejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initPropertyForms();
    initPropertyMaps();
    initBulkModerationSelectAll();
});

function initPropertyForms() {
    document.querySelectorAll('[data-property-form]').forEach((formRoot) => {
        const fileInput = formRoot.querySelector('[data-image-input]');
        const dropZone = formRoot.querySelector('[data-drop-zone]');
        const trigger = formRoot.querySelector('[data-trigger-upload]');
        const preview = formRoot.querySelector('[data-gallery-preview]');
        const manifestInput = formRoot.querySelector('[data-gallery-manifest]');
        const cropPayloadsInput = formRoot.querySelector('[data-crop-payloads]');
        const primaryInput = formRoot.querySelector('[data-primary-media-key]');
        const existing = JSON.parse(formRoot.dataset.existingImages || '[]');

        const state = {
            items: existing.map((item) => ({ ...item, crop: null, file: null })),
            primaryKey: primaryInput.value || existing.find((item) => item.is_primary)?.key || null,
        };

        const sortable = new Sortable(preview, {
            animation: 150,
            onEnd() {
                const ordered = [];
                preview.querySelectorAll('[data-media-key]').forEach((card) => {
                    const item = state.items.find((entry) => entry.key === card.dataset.mediaKey);
                    if (item) {
                        ordered.push(item);
                    }
                });
                state.items = ordered;
                syncHiddenFields();
            },
        });

        void sortable;

        trigger?.addEventListener('click', () => fileInput?.click());
        dropZone?.addEventListener('click', () => fileInput?.click());
        dropZone?.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropZone.classList.add('border-slate-900');
        });
        dropZone?.addEventListener('dragleave', () => dropZone.classList.remove('border-slate-900'));
        dropZone?.addEventListener('drop', (event) => {
            event.preventDefault();
            dropZone.classList.remove('border-slate-900');
            appendFiles(event.dataTransfer?.files);
        });

        fileInput?.addEventListener('change', (event) => {
            appendFiles(event.target.files);
        });

        formRoot.closest('form')?.addEventListener('submit', () => {
            const transfer = new DataTransfer();

            state.items
                .filter((item) => item.type === 'new' && item.file)
                .forEach((item) => transfer.items.add(item.file));

            if (fileInput) {
                fileInput.files = transfer.files;
            }

            syncHiddenFields();
        });

        render();

        function appendFiles(fileList) {
            if (!fileList) {
                return;
            }

            Array.from(fileList).forEach((file) => {
                if (state.items.length >= 20) {
                    return;
                }

                const key = `new-${crypto.randomUUID()}`;
                state.items.push({
                    type: 'new',
                    key,
                    preview: URL.createObjectURL(file),
                    name: file.name,
                    file,
                    crop: null,
                });

                if (!state.primaryKey) {
                    state.primaryKey = key;
                }
            });

            render();
        }

        function render() {
            preview.innerHTML = '';

            state.items.forEach((item) => {
                const card = document.createElement('div');
                card.className = 'overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm';
                card.dataset.mediaKey = item.key;
                card.innerHTML = `
                    <img src="${item.preview}" alt="${item.name}" class="h-40 w-full object-cover" loading="lazy">
                    <div class="space-y-3 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-sm font-semibold text-slate-800">${item.name}</p>
                            ${state.primaryKey === item.key ? '<span class="rounded-full bg-amber-50 px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-amber-800">Primary</span>' : ''}
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" data-action="primary" class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700">Make primary</button>
                            ${item.type === 'new' ? '<button type="button" data-action="crop" class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700">Crop</button>' : ''}
                            <button type="button" data-action="remove" class="rounded-full border border-red-300 px-3 py-1 text-xs font-semibold text-red-700">Delete</button>
                        </div>
                    </div>
                `;

                card.querySelector('[data-action="primary"]')?.addEventListener('click', () => {
                    state.primaryKey = item.key;
                    render();
                });

                card.querySelector('[data-action="remove"]')?.addEventListener('click', () => {
                    state.items = state.items.filter((entry) => entry.key !== item.key);
                    if (state.primaryKey === item.key) {
                        state.primaryKey = state.items[0]?.key || null;
                    }
                    render();
                });

                card.querySelector('[data-action="crop"]')?.addEventListener('click', () => openCropModal(item));
                preview.appendChild(card);
            });

            syncHiddenFields();
        }

        function syncHiddenFields() {
            manifestInput.value = JSON.stringify(state.items.map((item) => ({
                type: item.type,
                key: item.key,
                id: item.id || null,
            })));

            cropPayloadsInput.value = JSON.stringify(
                state.items.reduce((payloads, item) => {
                    if (item.crop) {
                        payloads[item.key] = item.crop;
                    }
                    return payloads;
                }, {}),
            );

            primaryInput.value = state.primaryKey || '';
        }

        function openCropModal(item) {
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-6';
            overlay.innerHTML = `
                <div class="w-full max-w-4xl rounded-3xl bg-white p-6 shadow-2xl">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Crop image</h3>
                        <button type="button" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700" data-close-modal>Close</button>
                    </div>
                    <div class="max-h-[65vh] overflow-hidden rounded-2xl bg-slate-100">
                        <img src="${item.preview}" alt="${item.name}" class="max-h-[65vh] w-full object-contain" data-crop-image>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white" data-save-crop>Save crop</button>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);
            const image = overlay.querySelector('[data-crop-image]');
            const cropper = new Cropper(image, {
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
            });

            overlay.querySelector('[data-close-modal]')?.addEventListener('click', () => {
                cropper.destroy();
                overlay.remove();
            });

            overlay.querySelector('[data-save-crop]')?.addEventListener('click', () => {
                item.crop = cropper.getData(true);
                cropper.destroy();
                overlay.remove();
                syncHiddenFields();
            });
        }
    });
}

function initPropertyMaps() {
    document.querySelectorAll('[data-properties-map]').forEach((element) => {
        const markers = JSON.parse(element.dataset.markers || '[]');

        if (!markers.length) {
            element.classList.add('flex', 'items-center', 'justify-center', 'bg-slate-100', 'text-slate-500');
            element.textContent = 'No map coordinates are available for the current result set.';
            return;
        }

        const map = L.map(element).setView([markers[0].latitude, markers[0].longitude], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const bounds = [];
        markers.forEach((marker) => {
            const leafletMarker = L.marker([marker.latitude, marker.longitude]).addTo(map);
            leafletMarker.bindPopup(`<a href="${marker.url}" class="font-semibold text-slate-900">${marker.title}</a>`);
            bounds.push([marker.latitude, marker.longitude]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [32, 32] });
        }
    });
}

function initBulkModerationSelectAll() {
    document.querySelectorAll('[data-check-all]').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const formId = checkbox.dataset.targetForm;
            document.querySelectorAll(`input[name="property_ids[]"][form="${formId}"]`).forEach((input) => {
                input.checked = checkbox.checked;
            });
        });
    });
}
