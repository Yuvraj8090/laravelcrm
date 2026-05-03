@extends('layouts.admin', ['heading' => 'Section Builder', 'subheading' => 'Drag, reorder, preview, and save reusable page sections.'])

@section('content')
    <form method="post" action="{{ route('admin.builder.update', $content) }}" class="grid-2" id="builder-form">
        @csrf
        @method('put')

        <section class="card">
            <div class="split">
                <h2 style="margin-top:0;">Sections</h2>
                <div class="actions">
                    <button type="button" class="secondary" onclick="addSection('hero')">Add Hero</button>
                    <button type="button" class="secondary" onclick="addSection('content')">Add Content</button>
                    <button type="button" class="secondary" onclick="togglePreviewWidth()">Mobile Preview</button>
                </div>
            </div>

            <div id="sections" class="stack">
                @foreach($content->sections as $section)
                    <div class="card section-item" draggable="true">
                        <div class="split">
                            <strong>{{ ucfirst($section->type) }}</strong>
                            <button type="button" class="danger" onclick="this.closest('.section-item').remove(); refreshIndexes(); renderPreview();">Remove</button>
                        </div>
                        <input type="hidden" data-field="type" name="sections[{{ $loop->index }}][type]" value="{{ $section->type }}">
                        <input type="hidden" data-field="sort_order" name="sections[{{ $loop->index }}][sort_order]" value="{{ $section->sort_order }}">
                        <div class="field"><label>Name</label><input data-field="name" name="sections[{{ $loop->index }}][name]" value="{{ $section->name }}"></div>
                        <div class="field"><label>Headline</label><input data-field="settings][headline" name="sections[{{ $loop->index }}][settings][headline]" value="{{ $section->settings['headline'] ?? '' }}" oninput="renderPreview()"></div>
                        <div class="field"><label>Body</label><textarea data-field="settings][body" name="sections[{{ $loop->index }}][settings][body]" oninput="renderPreview()">{{ $section->settings['body'] ?? '' }}</textarea></div>
                        <div class="grid-2">
                            <div class="field"><label>Background</label><input data-field="settings][background" name="sections[{{ $loop->index }}][settings][background]" value="{{ $section->settings['background'] ?? '#ffffff' }}" oninput="renderPreview()"></div>
                            <div class="field"><label>Text Color</label><input data-field="settings][color" name="sections[{{ $loop->index }}][settings][color]" value="{{ $section->settings['color'] ?? '#1d2b34' }}" oninput="renderPreview()"></div>
                        </div>
                        <label><input type="checkbox" data-field="is_reusable" name="sections[{{ $loop->index }}][is_reusable]" value="1" @checked($section->is_reusable) style="width:auto;"> Save as reusable section</label>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:1rem;">
                <button type="submit">Save builder layout</button>
            </div>
        </section>

        <section class="card">
            <h2 style="margin-top:0;">Live Preview</h2>
            <div id="preview-frame" style="margin:0 auto; max-width:100%; border:1px solid var(--line); border-radius:20px; overflow:hidden; background:white;">
                <div id="preview"></div>
            </div>

            <h3>Reusable Sections</h3>
            <div class="chip-list">
                @foreach($reusableSections as $section)
                    <button type="button" class="secondary" onclick='addReusable(@json($section->toArray()))'>{{ $section->name ?: ucfirst($section->type) }}</button>
                @endforeach
            </div>
        </section>
    </form>

    <template id="section-template">
        <div class="card section-item" draggable="true">
            <div class="split">
                <strong class="section-title">Section</strong>
                <button type="button" class="danger" onclick="this.closest('.section-item').remove(); refreshIndexes(); renderPreview();">Remove</button>
            </div>
            <input type="hidden" data-field="type">
            <input type="hidden" data-field="sort_order">
            <div class="field"><label>Name</label><input data-field="name"></div>
            <div class="field"><label>Headline</label><input data-field="settings][headline" oninput="renderPreview()"></div>
            <div class="field"><label>Body</label><textarea data-field="settings][body" oninput="renderPreview()"></textarea></div>
            <div class="grid-2">
                <div class="field"><label>Background</label><input data-field="settings][background" value="#ffffff" oninput="renderPreview()"></div>
                <div class="field"><label>Text Color</label><input data-field="settings][color" value="#1d2b34" oninput="renderPreview()"></div>
            </div>
            <label><input type="checkbox" data-field="is_reusable" value="1" style="width:auto;"> Save as reusable section</label>
        </div>
    </template>

    <script>
        const sections = document.getElementById('sections');
        let mobilePreview = false;

        function addSection(type, seed = {}) {
            const tpl = document.getElementById('section-template').content.cloneNode(true);
            const item = tpl.querySelector('.section-item');
            item.querySelector('.section-title').textContent = type.charAt(0).toUpperCase() + type.slice(1);
            sections.appendChild(item);
            refreshIndexes();
            const last = sections.lastElementChild;
            last.querySelector('[data-field="type"]').value = type;
            last.querySelector('[data-field="name"]').value = seed.name || '';
            last.querySelector('[data-field="settings][headline"]').value = seed.settings?.headline || '';
            last.querySelector('[data-field="settings][body"]').value = seed.settings?.body || '';
            last.querySelector('[data-field="settings][background"]').value = seed.settings?.background || '#ffffff';
            last.querySelector('[data-field="settings][color"]').value = seed.settings?.color || '#1d2b34';
            renderPreview();
            bindDrag();
        }

        function addReusable(section) {
            addSection(section.type, section);
        }

        function refreshIndexes() {
            [...sections.children].forEach((item, index) => {
                item.querySelectorAll('[data-field]').forEach((input) => {
                    const key = input.getAttribute('data-field');
                    input.name = `sections[${index}][${key}]`;
                });
                item.querySelector(`input[name="sections[${index}][sort_order]"]`).value = index;
            });
        }

        function renderPreview() {
            const preview = document.getElementById('preview');
            preview.innerHTML = '';
            [...sections.children].forEach((item) => {
                const headline = item.querySelector('input[name*="[headline]"]').value;
                const body = item.querySelector('textarea[name*="[body]"]').value;
                const background = item.querySelector('input[name*="[background]"]').value || '#ffffff';
                const color = item.querySelector('input[name*="[color]"]').value || '#1d2b34';
                const block = document.createElement('section');
                block.style.padding = '2rem';
                block.style.background = background;
                block.style.color = color;
                block.innerHTML = `<h3>${headline || 'Untitled section'}</h3><p>${body || 'Add some content here.'}</p>`;
                preview.appendChild(block);
            });
        }

        function togglePreviewWidth() {
            mobilePreview = !mobilePreview;
            document.getElementById('preview-frame').style.maxWidth = mobilePreview ? '390px' : '100%';
        }

        function bindDrag() {
            let dragged = null;
            [...sections.children].forEach((item) => {
                item.addEventListener('dragstart', () => dragged = item);
                item.addEventListener('dragover', (event) => event.preventDefault());
                item.addEventListener('drop', () => {
                    if (dragged && dragged !== item) {
                        sections.insertBefore(dragged, item);
                        refreshIndexes();
                        renderPreview();
                    }
                });
            });
        }

        bindDrag();
        refreshIndexes();
        renderPreview();
    </script>
@endsection
