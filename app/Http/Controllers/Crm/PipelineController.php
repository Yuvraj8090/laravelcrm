<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Pipeline;
use App\Crm\Models\PipelineStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PipelineController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $pipelines = Pipeline::with('stages')->orderByDesc('is_default')->orderBy('name')->get();
        $pipeline = $request->query('edit')
            ? Pipeline::with('stages')->findOrFail((int) $request->query('edit'))
            : new Pipeline(['is_default' => $pipelines->isEmpty()]);

        return $this->crmView('crm.pipelines.index', compact('pipelines', 'pipeline'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $pipeline = Pipeline::create([
            'name' => $validated['name'],
            'is_default' => $request->boolean('is_default'),
        ]);
        $this->syncDefault($pipeline);
        $this->syncStages($pipeline, $validated['stages'] ?? []);

        return back()->with('status', 'Pipeline saved.');
    }

    public function update(Request $request, Pipeline $pipeline): RedirectResponse
    {
        $validated = $this->validated($request);
        $pipeline->update([
            'name' => $validated['name'],
            'is_default' => $request->boolean('is_default'),
        ]);
        $this->syncDefault($pipeline);
        $pipeline->stages()->delete();
        $this->syncStages($pipeline, $validated['stages'] ?? []);

        return redirect()->route('crm.pipelines.index')->with('status', 'Pipeline updated.');
    }

    public function destroy(Pipeline $pipeline): RedirectResponse
    {
        $pipeline->delete();

        return back()->with('status', 'Pipeline deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stages' => ['nullable', 'array'],
            'stages.*.name' => ['required', 'string', 'max:255'],
            'stages.*.position' => ['required', 'integer'],
            'stages.*.probability' => ['required', 'integer'],
            'stages.*.color' => ['required', 'string', 'max:20'],
        ]);
    }

    protected function syncDefault(Pipeline $pipeline): void
    {
        if ($pipeline->is_default) {
            Pipeline::whereKeyNot($pipeline->id)->update(['is_default' => false]);
        }
    }

    protected function syncStages(Pipeline $pipeline, array $stages): void
    {
        foreach ($stages as $stage) {
            PipelineStage::create([
                'pipeline_id' => $pipeline->id,
                'name' => $stage['name'],
                'position' => $stage['position'],
                'probability' => $stage['probability'],
                'color' => $stage['color'],
            ]);
        }
    }
}
