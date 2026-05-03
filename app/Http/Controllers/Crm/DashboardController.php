<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Activity;
use App\Crm\Models\Company;
use App\Crm\Models\Contact;
use App\Crm\Models\Deal;
use App\Crm\Models\Lead;
use App\Crm\Models\PipelineStage;
use App\Crm\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends BaseCrmController
{
    public function __invoke(): View
    {
        $metrics = Cache::remember('crm-dashboard-metrics', now()->addMinutes(10), function () {
            return [
                'leads' => Lead::count(),
                'contacts' => Contact::count(),
                'companies' => Company::count(),
                'open_deal_value' => Deal::where('status', 'open')->sum('value'),
                'tasks_due' => Task::where('status', '!=', 'completed')->count(),
            ];
        });

        $pipeline = PipelineStage::withCount('deals')->orderBy('position')->get();
        $recentActivities = Activity::with('user')->latest()->limit(8)->get();

        return $this->crmView('crm.dashboard', compact('metrics', 'pipeline', 'recentActivities'));
    }
}
