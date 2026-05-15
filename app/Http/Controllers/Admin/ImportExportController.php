<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\BooksImport;
use App\Imports\UsersImport;
use App\Exports\BooksExport;
use App\Exports\OrdersExport;
use App\Exports\UsersExport;
use App\Exports\AuditLogExport;
use App\Models\Book;
use App\Models\Category;
use App\Models\ImportLog;
use App\Models\ExportLog;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;

class ImportExportController extends Controller
{
    // ─── Book Import ──────────────────────────────────────────────
    public function importForm()
    {
        return view('admin.import-export.import');
    }

    public function importBooks(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
            'update_existing' => 'nullable|boolean',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('imports');

        $importLog = ImportLog::create([
            'user_id' => auth()->id(),
            'filename' => $originalName,
            'type' => 'book',
            'total_rows' => 0,
            'status' => 'processing',
            'file_path' => $path,
        ]);

        $import = new BooksImport($importLog, $request->boolean('update_existing'));

        try {
            Excel::import($import, Storage::disk('local')->path($path));

            $failures = $import->failures();
            $failureRows = collect($failures)->map(fn($f) => [
                'row' => $f->row(),
                'attribute' => $f->attribute(),
                'errors' => $f->errors(),
            ]);

            $importLog->update([
                'status' => $failures->isNotEmpty() ? 'completed_with_errors' : 'completed',
                'total_rows' => $importLog->processed_rows + $failureRows->count(),
                'failures' => $failureRows,
                'failed_rows' => $failureRows->count(),
            ]);

            $message = 'Import completed. ' . $failureRows->count() . ' failures.' . $this->failureSummary($failureRows);
        } catch (\Exception $e) {
            $importLog->update([
                'status' => 'failed',
                'failures' => [['error' => $e->getMessage()]],
            ]);
            $message = 'Book import failed: ' . $e->getMessage();
        }

        return redirect()->route('admin.import-export.import')
            ->with($importLog->status === 'failed' ? 'error' : 'success', $message);
    }

    public function downloadTemplate()
    {
        $headers = ['isbn', 'title', 'author', 'price', 'stock', 'category', 'format', 'description'];
        $sampleData = [
            ['978-0-1234-5678-9', 'Sample Book', 'Author Name', '19.99', '10', 'Fiction', 'paperback', 'A sample book description.'],
        ];

        return Excel::download(
            new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $headers;
                protected $data;
                public function __construct($headers, $data) { $this->headers = $headers; $this->data = $data; }
                public function array(): array { return $this->data; }
                public function headings(): array { return $this->headers; }
            },
            'book-import-template.xlsx'
        );
    }

    // ─── Book Export ──────────────────────────────────────────────
    public function exportForm()
    {
        $categories = Category::all();
        return view('admin.import-export.export', compact('categories'));
    }

    public function exportBooks(Request $request)
    {
        // Increase execution time for large exports
        set_time_limit(300);

        $request->validate([
            'format' => ['nullable', Rule::in(['xlsx', 'csv'])],
            'category_id' => ['nullable', 'exists:categories,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'stock_status' => ['nullable', Rule::in(['in_stock', 'out_of_stock'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $filters = $request->only(['category_id', 'min_price', 'max_price', 'stock_status', 'date_from', 'date_to']);
        $format = $request->input('format', 'xlsx');

        $exportLog = ExportLog::create([
            'user_id' => auth()->id(),
            'type' => 'book',
            'format' => $format,
            'status' => 'processing',
            'filters' => $filters,
        ]);

        $export = new BooksExport($filters);
        $fileName = 'books-export-' . now()->format('Y-m-d-His') . '.' . $format;

        try {
            Excel::store($export, $fileName, 'public', $this->writerType($format));

            $exportLog->update([
                'status' => 'completed',
                'file_path' => $fileName,
            ]);

            return redirect()->route('admin.import-export.exports')
                ->with('success', 'Book export completed!');
        } catch (\Exception $e) {
            $exportLog->update(['status' => 'failed']);

            return redirect()->route('admin.import-export.export')
                ->with('error', 'Book export failed: ' . $e->getMessage());
        }
    }

    // ─── Order Export ─────────────────────────────────────────────
    public function exportOrders(Request $request)
    {
        // Increase execution time for large exports
        set_time_limit(300);

        $request->validate([
            'format' => ['nullable', Rule::in(['xlsx', 'csv'])],
            'status' => ['nullable', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $filters = $request->only(['status', 'date_from', 'date_to', 'user_id']);
        $format = $request->input('format', 'xlsx');

        $exportLog = ExportLog::create([
            'user_id' => auth()->id(),
            'type' => 'order',
            'format' => $format,
            'status' => 'processing',
            'filters' => $filters,
        ]);

        $export = new OrdersExport($filters);
        $fileName = 'orders-export-' . now()->format('Y-m-d-His') . '.' . $format;
        try {
            Excel::store($export, $fileName, 'public', $this->writerType($format));

            $exportLog->update([
                'status' => 'completed',
                'file_path' => $fileName,
            ]);

            return redirect()->route('admin.import-export.exports')
                ->with('success', 'Order export completed!');
        } catch (\Exception $e) {
            $exportLog->update(['status' => 'failed']);

            return redirect()->route('admin.import-export.export')
                ->with('error', 'Order export failed: ' . $e->getMessage());
        }
    }

    // ─── User Import ──────────────────────────────────────────────
    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        $file = $request->file('file');
        $path = $file->store('imports');

        $importLog = ImportLog::create([
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'type' => 'user',
            'status' => 'pending',
            'file_path' => $path,
        ]);

        $import = new UsersImport($importLog);

        try {
            Excel::import($import, Storage::disk('local')->path($path));

            $failures = $import->failures();
            $failureRows = collect($failures)->map(fn($f) => [
                'row' => $f->row(),
                'attribute' => $f->attribute(),
                'errors' => $f->errors(),
            ]);

            $importLog->update([
                'status' => $failures->isNotEmpty() ? 'completed_with_errors' : 'completed',
                'total_rows' => $importLog->processed_rows + $failureRows->count(),
                'failures' => $failureRows,
                'failed_rows' => $failureRows->count(),
            ]);

            $message = 'User import completed. ' . $failureRows->count() . ' failures.' . $this->failureSummary($failureRows);
        } catch (\Exception $e) {
            $importLog->update([
                'status' => 'failed',
                'failures' => [['error' => $e->getMessage()]],
            ]);
            $message = 'User import failed: ' . $e->getMessage();
        }

        return redirect()->route('admin.import-export.import')
            ->with($importLog->status === 'failed' ? 'error' : 'success', $message);
    }

    // ─── User Export ──────────────────────────────────────────────
    public function exportUsers(Request $request)
    {
        // Increase execution time for large exports
        set_time_limit(300);

        $request->validate([
            'format' => ['nullable', Rule::in(['xlsx', 'csv'])],
            'redact_pii' => ['nullable', 'boolean'],
        ]);

        $redactPii = $request->boolean('redact_pii');
        $format = $request->input('format', 'xlsx');

        $exportLog = ExportLog::create([
            'user_id' => auth()->id(),
            'type' => 'user',
            'format' => $format,
            'status' => 'processing',
            'filters' => ['redact_pii' => $redactPii],
        ]);

        $export = new UsersExport($redactPii);
        $fileName = 'users-export-' . now()->format('Y-m-d-His') . '.' . $format;
        try {
            Excel::store($export, $fileName, 'public', $this->writerType($format));

            $exportLog->update([
                'status' => 'completed',
                'file_path' => $fileName,
            ]);

            return redirect()->route('admin.import-export.exports')
                ->with('success', 'User export completed!');
        } catch (\Exception $e) {
            $exportLog->update(['status' => 'failed']);

            return redirect()->route('admin.import-export.export')
                ->with('error', 'User export failed: ' . $e->getMessage());
        }
    }

    // ─── Download Export ─────────────────────────────────────────
    public function downloadExport(ExportLog $exportLog)
    {
        abort_if($exportLog->status !== 'completed' || !$exportLog->file_path, 404);
        abort_if(!Storage::disk('public')->exists($exportLog->file_path), 404);

        return Storage::disk('public')->download($exportLog->file_path);
    }

    // ─── Export Logs ─────────────────────────────────────────────
    public function exportLogs()
    {
        $exports = ExportLog::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.import-export.export-logs', compact('exports'));
    }

    // ─── Import Logs ─────────────────────────────────────────────
    public function importLogs()
    {
        $imports = ImportLog::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.import-export.import-logs', compact('imports'));
    }

    // ─── Customer Export My Data ─────────────────────────────────
    public function exportMyData()
    {
        $user = auth()->user();
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'orders' => $user->orders()->with('orderItems.book')->get()->toArray(),
            'reviews' => $user->reviews()->with('book')->get()->toArray(),
        ];

        $exportLog = ExportLog::create([
            'user_id' => $user->id,
            'type' => 'user',
            'format' => 'json',
            'status' => 'completed',
        ]);

        return response()->json($data);
    }

    public function exportMyOrders(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $filters = ['user_id' => auth()->id()];

        $export = new OrdersExport($filters);
        $fileName = 'my-orders-' . now()->format('Y-m-d-His') . '.' . $format;
        Excel::store($export, $fileName, 'public', $this->writerType($format));

        abort_if(!Storage::disk('public')->exists($fileName), 404);

        return Storage::disk('public')->download($fileName);
    }

    public function downloadInvoice(Order $order)
    {
        if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load('orderItems.book', 'user');
        $pdf = Pdf::loadView('admin.import-export.invoice', compact('order'));
        return $pdf->download('invoice-' . $order->id . '.pdf');
    }

    private function writerType(string $format): string
    {
        return $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
    }

    private function failureSummary($failureRows): string
    {
        if ($failureRows->isEmpty()) {
            return '';
        }

        $first = $failureRows->first();
        $errors = implode(' ', $first['errors'] ?? []);

        return " First failure: row {$first['row']} ({$first['attribute']}) - {$errors}";
    }
}
