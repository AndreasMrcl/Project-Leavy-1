$(document).ready(function () {
    if (typeof window.menusData === 'undefined') return;

    const menusData = window.menusData;
    const upsertUrlTemplate = window.upsertUrlTemplate;
    const deleteUrlTemplate = window.deleteUrlTemplate;
    const ingredientModal = $('#ingredientModal');

    let selectedMenu = null;
    let activeVariety = null;
    let recipe = {};

    const labelize = (slug) => String(slug).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    const fmtQty = (n) => {
        const s = Number(n).toString();
        return s.includes('.') ? s.replace(/\.?0+$/, '') : s;
    };

    // ========== Search ==========
    $('#menuSearch').on('input', function () {
        const q = $(this).val().toLowerCase();
        $('.menuCard').each(function () {
            const n = $(this).data('name-lower');
            $(this).toggle(String(n).includes(q));
        });
    });

    // ========== Pilih menu ==========
    $('.menuCard').click(function () {
        const card = $(this);
        const newId = card.data('id');
        if (selectedMenu && selectedMenu.id == newId) return;

        if (isDirty()) {
            Swal.fire({
                title: 'Pindah produk?',
                text: 'Perubahan resep yang belum disimpan akan hilang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, pindah',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) selectMenu(newId);
            });
            return;
        }

        selectMenu(newId);
    });

    function selectMenu(id) {
        const data = menusData[id];
        if (!data) return;

        $('.menuCard').removeClass('border-red-500 ring-2 ring-red-200');
        $(`.menuCard[data-id="${id}"]`).addClass('border-red-500 ring-2 ring-red-200');

        selectedMenu = data;
        activeVariety = data.varieties[0];

        recipe = {};
        data.varieties.forEach(v => {
            recipe[v] = (data.recipe[v] || []).map(r => ({ ...r }));
        });

        $('#ingredientForm').attr('action', upsertUrlTemplate.replace('__ID__', id));
        $('#deleteForm').attr('action', deleteUrlTemplate.replace('__ID__', id));
        $('#compProductName').text(data.name);
        $('#emptyState').addClass('hidden');
        $('#compositionArea').removeClass('hidden');

        $('#submitLabel').text(data.has_recipe ? 'Update Resep' : 'Simpan Resep');
        $('#deleteBtn').toggleClass('hidden', !data.has_recipe).toggleClass('flex', data.has_recipe);

        renderVarietyTabs();
        renderPanels();
        refreshSubmitState();
    }

    function isDirty() {
        if (!selectedMenu) return false;
        const original = selectedMenu.recipe;
        for (const v of selectedMenu.varieties) {
            const a = (original[v] || []).map(r => `${r.invent_id}:${r.qty}`).sort().join('|');
            const b = (recipe[v] || []).map(r => `${r.invent_id}:${r.qty}`).sort().join('|');
            if (a !== b) return true;
        }
        return false;
    }

    // ========== Variety tabs ==========
    function renderVarietyTabs() {
        const $tabs = $('#varietyTabs');
        const $list = $('#varietyTabsList').empty();

        if (selectedMenu.varieties.length <= 1) {
            $tabs.addClass('hidden');
            return;
        }
        $tabs.removeClass('hidden');

        selectedMenu.varieties.forEach((v) => {
            const active = v === activeVariety;
            $list.append(`
                <button type="button" data-variety="${v}"
                    class="varietyTab whitespace-nowrap px-4 py-2 font-semibold text-sm border-b-2 ${active ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700'}">
                    ${labelize(v)}
                </button>
            `);
        });
    }

    $(document).on('click', '.varietyTab', function () {
        activeVariety = $(this).data('variety');
        renderVarietyTabs();
        renderPanels();
    });

    // ========== Panels ==========
    function renderPanels() {
        const $panels = $('#varietyPanels').empty();
        const items = recipe[activeVariety] || [];

        if (items.length === 0) {
            $panels.append(`
                <div class="text-center py-6 text-gray-400 text-sm border border-dashed border-gray-200 rounded-lg">
                    <i class="fas fa-list-ul text-2xl block mb-2"></i>
                    Belum ada bahan${selectedMenu.varieties.length > 1 ? ' di variety ini' : ''}.
                </div>
            `);
            return;
        }

        items.forEach((item, idx) => {
            $panels.append(`
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 border border-gray-100">
                    <div class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center text-xs font-bold shrink-0">
                        ${(item.unit || '').slice(0, 2).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">${item.name}</p>
                        <p class="text-xs text-gray-500">${fmtQty(item.qty)} ${item.unit}</p>
                    </div>
                    <button type="button" class="removeItem w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 rounded-lg transition" data-idx="${idx}" title="Hapus">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            `);
        });
    }

    $(document).on('click', '.removeItem', function () {
        const idx = $(this).data('idx');
        recipe[activeVariety].splice(idx, 1);
        renderPanels();
        refreshSubmitState();
    });

    function refreshSubmitState() {
        const total = Object.values(recipe).reduce((sum, arr) => sum + arr.length, 0);
        $('#submitBtn').prop('disabled', total === 0);
    }

    // ========== Modal ==========
    $('#openModalBtn').click(function () {
        if (!selectedMenu) return;
        const hint = selectedMenu.varieties.length > 1
            ? `Untuk variety: <strong>${labelize(activeVariety)}</strong>`
            : '';
        $('#modalVarietyHint').html(hint);
        $('#modalBahanSelect').val('');
        $('#modalQty').val('');
        $('#modalUnit').text('');
        ingredientModal.removeClass('hidden');
    });

    $('#closeIngredientModal').click(() => ingredientModal.addClass('hidden'));
    ingredientModal.on('click', function (e) {
        if (!$(e.target).closest('.shadow-2xl').length) ingredientModal.addClass('hidden');
    });

    $('#modalBahanSelect').on('change', function () {
        const opt = $(this).find(':selected');
        $('#modalUnit').text(opt.data('unit') || '');
    });

    $('#modalAddBtn').click(function () {
        const $sel = $('#modalBahanSelect');
        const opt = $sel.find(':selected');
        const inventId = $sel.val();
        const qty = parseFloat($('#modalQty').val());

        if (!inventId) {
            return Swal.fire({ icon: 'warning', title: 'Pilih bahan dulu', confirmButtonColor: '#ef4444' });
        }
        if (!qty || qty <= 0) {
            return Swal.fire({ icon: 'warning', title: 'Jumlah tidak valid', text: 'Isi jumlah lebih dari 0.', confirmButtonColor: '#ef4444' });
        }

        const dup = recipe[activeVariety].some(it => it.invent_id == inventId);
        if (dup) {
            return Swal.fire({
                icon: 'warning',
                title: 'Bahan sudah ada',
                text: 'Bahan ini sudah ditambahkan di variety ini. Hapus dulu jika ingin diganti.',
                confirmButtonColor: '#ef4444',
            });
        }

        recipe[activeVariety].push({
            invent_id: String(inventId),
            name: opt.data('name'),
            unit: opt.data('unit'),
            qty: qty,
        });

        renderPanels();
        refreshSubmitState();
        ingredientModal.addClass('hidden');
    });

    // ========== Delete ==========
    $('#deleteBtn').click(function () {
        if (!selectedMenu) return;
        Swal.fire({
            title: 'Hapus resep ini?',
            text: `Komposisi bahan untuk "${selectedMenu.name}" akan dihapus.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) $('#deleteForm').submit();
        });
    });

    // ========== Submit: serialize state into hidden inputs ==========
    $('#ingredientForm').on('submit', function (e) {
        const $form = $(this);
        $form.find('.dynamic-input').remove();

        const total = Object.values(recipe).reduce((sum, arr) => sum + arr.length, 0);
        if (total === 0) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Belum ada bahan', text: 'Tambahkan minimal 1 bahan.', confirmButtonColor: '#ef4444' });
            return;
        }

        Object.entries(recipe).forEach(([variety, items]) => {
            items.forEach((item, idx) => {
                $form.append(`<input type="hidden" class="dynamic-input" name="ingredients[${variety}][${idx}][invent_id]" value="${item.invent_id}">`);
                $form.append(`<input type="hidden" class="dynamic-input" name="ingredients[${variety}][${idx}][quantity_used]" value="${item.qty}">`);
            });
        });
    });
});
