<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIReport;
use App\Models\AIUsageLog;
use App\Jobs\GenerateAIReport;
use App\Services\AIReportGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIReportController extends Controller
{
    protected AIReportGeneratorService $reportGenerator;

    public function __construct(AIReportGeneratorService $reportGenerator)
    {
        $this->reportGenerator = $reportGenerator;
    }

    public function index()
    {
        $reports = AIReport::with('user')
            ->recent()
            ->paginate(15);

        $usageToday = AIUsageLog::whereDate('created_at', today())->sum('tokens_used');
        $usageThisWeek = AIUsageLog::where('created_at', '>=', now()->startOfWeek())->sum('tokens_used');
        $totalReports = AIReport::count();
        $completedReports = AIReport::where('status', 'completed')->count();

        return view('admin.ai-reports.index', compact(
            'reports', 'usageToday', 'usageThisWeek', 'totalReports', 'completedReports'
        ));
    }

    public function create()
    {
        return view('admin.ai-reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string|in:overview,sales,inventory,users,reviews,categories,bestsellers,alerts',
            'period' => 'sometimes|string|in:this_month,last_month,last_3_months,this_year,all_time',
            'category_id' => 'sometimes|nullable|integer|exists:categories,id',
            'format' => 'sometimes|string|in:concise,detailed',
            'model' => 'sometimes|nullable|string',
            'async' => 'sometimes|boolean',
        ]);

        $async = $request->boolean('async', false);

        $query = $this->buildQueryFromFilters($request);

        $modelInput = $request->input('model');
        if ($modelInput && str_contains($modelInput, ':')) {
            [$provider, $modelName] = explode(':', $modelInput, 2);
            $this->reportGenerator->setProvider($provider);
            $this->reportGenerator->setModel($modelName);
        } elseif ($modelInput) {
            $this->reportGenerator->setModel($modelInput);
        }

        try {
            $report = $this->reportGenerator->generateReport(
                auth()->id(),
                $query,
                !$async
            );

            if ($async) {
                GenerateAIReport::dispatch($report);
                return redirect()->route('admin.ai-reports.index')
                    ->with('success', 'Report generation has been queued. You will be notified when it is ready.');
            }

            if ($report->status === 'failed') {
                return redirect()->route('admin.ai-reports.index')
                    ->with('error', 'Report generation failed: ' . $report->error_message);
            }

            return redirect()->route('admin.ai-reports.show', $report)
                ->with('success', 'Report generated successfully!');

        } catch (\Exception $e) {
            Log::error('AI Report generation error: ' . $e->getMessage());

            return redirect()->route('admin.ai-reports.index')
                ->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    public function show(AIReport $report)
    {
        $report->load('user');
        return view('admin.ai-reports.show', compact('report'));
    }

    public function showPrint(AIReport $report)
    {
        $report->load('user');
        return view('admin.ai-reports.print', compact('report'));
    }

    public function downloadWord(AIReport $report)
    {
        $report->load('user');
        $html = view('admin.ai-reports.print', compact('report'))->render();

        $filename = 'report-' . $report->id . '-' . now()->format('Y-m-d') . '.doc';

        return response($html, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function regenerate(Request $request, AIReport $report)
    {
        $report->update([
            'status' => 'generating',
            'error_message' => null,
            'completed_at' => null,
        ]);

        try {
            $this->reportGenerator->processReport($report);

            if ($report->status === 'failed') {
                return redirect()->route('admin.ai-reports.show', $report)
                    ->with('error', 'Regeneration failed: ' . $report->error_message);
            }

            return redirect()->route('admin.ai-reports.show', $report)
                ->with('success', 'Report regenerated successfully!');

        } catch (\Exception $e) {
            return redirect()->route('admin.ai-reports.show', $report)
                ->with('error', 'Regeneration failed: ' . $e->getMessage());
        }
    }

    public function destroy(AIReport $report)
    {
        $report->delete();

        return redirect()->route('admin.ai-reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    public function usageLogs()
    {
        $logs = AIUsageLog::with('user')
            ->recent()
            ->paginate(30);

        $totalTokens = AIUsageLog::sum('tokens_used');
        $totalCost = AIUsageLog::sum('cost_estimate');
        $todayTokens = AIUsageLog::whereDate('created_at', today())->sum('tokens_used');
        $successRate = $this->getSuccessRate();

        return view('admin.ai-reports.usage', compact(
            'logs', 'totalTokens', 'totalCost', 'todayTokens', 'successRate'
        ));
    }

    protected function buildQueryFromFilters(Request $request): string
    {
        $type = $request->input('report_type');
        $period = $request->input('period', 'this_month');
        $categoryId = $request->input('category_id');
        $format = $request->input('format', 'concise');

        $periodLabel = match ($period) {
            'this_month' => 'this month',
            'last_month' => 'last month',
            'last_3_months' => 'the last 3 months',
            'this_year' => 'this year',
            'all_time' => 'all time',
        };

        $queries = [
            'overview' => "Show me a complete business overview for {$periodLabel} including total sales, revenue, order statistics, user counts, inventory levels, and top performing categories." . ($categoryId ? " Focus specifically on category #{$categoryId}." : ''),
            'sales' => "Analyze sales and revenue for {$periodLabel} including total revenue, average order value, number of orders by status, monthly revenue trends, and order status breakdown." . ($categoryId ? " Filter by category #{$categoryId}." : ''),
            'inventory' => "Show complete inventory status including total books, active books, total stock units, average price, low stock count, out of stock count, total inventory value, and category breakdown." . ($categoryId ? " Focus on category #{$categoryId}." : ''),
            'users' => "Show user analytics for {$periodLabel} including total users by role, new registrations, verified users, and growth trends." . ($categoryId ? " Include users who ordered from category #{$categoryId}." : ''),
            'reviews' => "Analyze book reviews for {$periodLabel} including total reviews, average rating, rating distribution, and review trends." . ($categoryId ? " Filter by category #{$categoryId}." : ''),
            'categories' => "Show category performance for {$periodLabel} including book counts per category, and identify the top and bottom performing categories." . ($categoryId ? " Focus on category #{$categoryId}." : ''),
            'bestsellers' => "Show the top selling books for {$periodLabel} ranked by quantity sold, including title, author, and total sold." . ($categoryId ? " Filter by category #{$categoryId}." : ''),
            'alerts' => "Show low stock alerts for books with stock quantity below 10, including which books need immediate reordering, their current stock, and priority recommendations." . ($categoryId ? " Focus on category #{$categoryId}." : ''),
        ];

        $query = $queries[$type] ?? $queries['overview'];

        if ($format === 'detailed') {
            $query .= ' Provide a very detailed analysis with granular breakdowns.';
        }

        return $query;
    }

    protected function getSuccessRate(): float
    {
        $total = AIUsageLog::count();
        if ($total === 0) {
            return 100;
        }
        $successful = AIUsageLog::where('success', true)->count();
        return round(($successful / $total) * 100, 1);
    }
}
