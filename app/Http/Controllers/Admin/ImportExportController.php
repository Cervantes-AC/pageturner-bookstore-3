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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            'file' => 'required|file|mimes:xlsx,csv',
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
            'status' => 'pending',
            'file_path' => $path,
        ]);

        $import = new BooksImport($importLog, $request->boolean('update_existing'));
        Excel::import($import, storage_path('app/' . $path));

        $failures = $import->failures();
        $failureRows = collect($failures)->map(fn($f) => [
            'row' => $f->row(),
            'attribute' => $f->attribute(),
            'errors' => $f->errors(),
        ]);

        $importLog->update([
            'status' => $failures->isNotEmpty() ? 'completed_with_errors' : 'completed',
            'failures' => $failureRows,
            'failed_rows' => $failureRows->count(),
        ]);

        return redirect()->route('admin.import-export.import')
            ->with('success', 'Import completed. ' . $failureRows->count() . ' failures.');
    }

    public function downloadTemplate()
    {
        $headers = ['isbn', 'title', 'author', 'price', 'stock', 'category', 'description'];
        $sampleData = [
            ['978-0-1234-5678-9', 'Sample Book', 'Author Name', '19.99', '10', 'Fiction', 'A sample book description.'],
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

        if ($format === 'csv') {
            Excel::store($export, $fileName, 'public', \Maatwebsite\Excel\Excel::CSV);
        } else {
            Excel::store($export, $fileName, 'public');
        }

        $exportLog->update([
            'status' => 'completed',
            'file_path' => $fileName,
        ]);

        return redirect()->route('admin.import-export.exports')
            ->with('success', 'Export completed!');
    }

    // ─── Order Export ─────────────────────────────────────────────
    public function exportOrders(Request $request)
    {
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
        Excel::store($export, $fileName, 'public');

        $exportLog->update([
            'status' => 'completed',
            'file_path' => $fileName,
        ]);

        return redirect()->route('admin.import-export.exports')
            ->with('success', 'Order export completed!');
    }

    // ─── User Import ──────────────────────────────────────────────
    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
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
        Excel::import($import, storage_path('app/' . $path));

        $importLog->update(['status' => 'completed']);

        return redirect()->route('admin.import-export.import')
            ->with('success', 'User import completed!');
    }

    // ─── User Export ──────────────────────────────────────────────
    public function exportUsers(Request $request)
    {
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
        Excel::store($export, $fileName, 'public');

        $exportLog->update([
            'status' => 'completed',
            'file_path' => $fileName,
        ]);

        return redirect()->route('admin.import-export.exports')
            ->with('success', 'User export completed!');
    }

    // ─── Download Export ─────────────────────────────────────────
    public function downloadExport(ExportLog $exportLog)
    {
        return response()->download(storage_path('app/public/' . $exportLog->file_path));
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
        Excel::store($export, $fileName, 'public');

        return response()->download(storage_path('app/public/' . $fileName));
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
}
