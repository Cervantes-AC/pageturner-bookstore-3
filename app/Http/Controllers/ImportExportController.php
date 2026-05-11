<?php

namespace App\Http\Controllers;

use App\Exports\BooksExport;
use App\Exports\OrdersExport;
use App\Exports\UsersExport;
use App\Imports\BooksImport;
use App\Models\Book;
use App\Models\ExportLog;
use App\Models\ImportLog;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    // ── Import ────────────────────────────────────────────────

    public function importForm()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $logs = ImportLog::with('user')->latest()->take(20)->get();
        return view('import-export.import', compact('logs'));
    }

    public function importBooks(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:51200',
            'mode' => 'required|in:skip,update',
        ]);

        $file = $request->file('file');
        $log  = ImportLog::create([
            'user_id'  => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'type'     => 'books',
            'status'   => 'processing',
            'mode'     => $request->mode,
        ]);

        try {
            $import = new BooksImport($log->id, $request->mode);
            Excel::import($import, $file);

            // Count rows from the spreadsheet
            $rowCount = Excel::toArray(new \App\Imports\BooksRowCounter(), $file)[0] ?? [];
            $total    = max(0, count($rowCount) - 1);

            $log->update([
                'status'         => 'completed',
                'total_rows'     => $total,
                'processed_rows' => $total - ($log->fresh()->failed_rows ?? 0),
            ]);
        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'errors' => [['message' => $e->getMessage()]]]);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Import completed. Check the log for details.');
    }

    public function downloadTemplate()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $headers = [['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category', 'Description']];
        $sample  = [['978-0-0000-0001-0', 'Sample Book', 'Author Name', '19.99', '100', 'Fiction', 'A sample description']];

        return Excel::download(
            new class($headers, $sample) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                public function __construct(private array $h, private array $s) {}
                public function array(): array { return array_merge($this->h, $this->s); }
            },
            'books_import_template.xlsx'
        );
    }

    // ── Export ────────────────────────────────────────────────

    public function exportForm()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $logs = ExportLog::with('user')->latest()->take(20)->get();
        $orderFormats = ['xlsx', 'csv', 'pdf'];
        return view('import-export.export', compact('logs', 'orderFormats'));
    }

    public function exportBooks(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate([
            'format'  => 'required|in:xlsx,csv',
            'columns' => 'nullable|array',
        ]);

        $filters = $request->only(['category_id', 'min_price', 'max_price', 'in_stock', 'date_from', 'date_to']);
        $columns = $request->input('columns', []);

        $bookCount = Book::count();
        $isLarge   = $bookCount > 10000;

        $log = ExportLog::create([
            'user_id' => auth()->id(),
            'type'    => 'books',
            'format'  => $request->format,
            'filters' => $filters,
            'status'  => $isLarge ? 'processing' : 'completed',
        ]);

        $filename = 'books_export_' . now()->format('Ymd_His') . '.' . $request->format;

        if ($isLarge) {
            dispatch(function () use ($log, $filters, $columns, $filename, $request) {
                try {
                    Excel::store(new BooksExport($filters, $columns), $filename, 'public');
                    $log->update([
                        'status'       => 'completed',
                        'download_link' => '/storage/' . $filename,
                    ]);
                } catch (\Exception $e) {
                    $log->update(['status' => 'failed']);
                }
            });

            return back()->with('info', "Export queued for {$bookCount} records. You'll be notified when ready.");
        }

        return Excel::download(new BooksExport($filters, $columns), $filename);
    }

    public function exportOrders(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate(['format' => 'required|in:xlsx,csv,pdf']);

        $filters = $request->only(['status', 'date_from', 'date_to']);

        if ($request->format === 'pdf') {
            $query = Order::with(['user', 'orderItems.book'])->latest();
            if (!empty($filters['status']))    $query->where('status', $filters['status']);
            if (!empty($filters['date_from'])) $query->whereDate('created_at', '>=', $filters['date_from']);
            if (!empty($filters['date_to']))   $query->whereDate('created_at', '<=', $filters['date_to']);

            $orders  = $query->take(500)->get();
            $revenue = $orders->sum('total_amount');
            $pdf     = Pdf::loadView('orders.export-pdf', compact('orders', 'filters', 'revenue'))
                ->setPaper('a4', 'landscape');

            ExportLog::create([
                'user_id' => auth()->id(),
                'type'    => 'orders',
                'format'  => 'pdf',
                'filters' => $filters,
                'status'  => 'completed',
            ]);

            return $pdf->download('orders_export_' . now()->format('Ymd_His') . '.pdf');
        }

        ExportLog::create([
            'user_id' => auth()->id(),
            'type'    => 'orders',
            'format'  => $request->format,
            'filters' => $filters,
            'status'  => 'completed',
        ]);

        $filename = 'orders_export_' . now()->format('Ymd_His') . '.' . $request->format;

        return Excel::download(new OrdersExport($filters), $filename);
    }

    public function exportMyOrders(Request $request)
    {
        $request->validate(['format' => 'required|in:xlsx,csv,pdf']);

        ExportLog::create([
            'user_id' => auth()->id(),
            'type'    => 'my_orders',
            'format'  => $request->format,
            'filters' => [],
            'status'  => 'completed',
        ]);

        if ($request->format === 'pdf') {
            $orders  = auth()->user()->orders()->with('orderItems.book')->latest()->get();
            $revenue = $orders->sum('total_amount');
            $pdf     = Pdf::loadView('orders.export-pdf', compact('orders', 'revenue'))
                ->setPaper('a4', 'portrait');

            return $pdf->download('my_orders_' . now()->format('Ymd_His') . '.pdf');
        }

        $filename = 'my_orders_' . now()->format('Ymd_His') . '.' . $request->format;

        return Excel::download(new OrdersExport([], auth()->id()), $filename);
    }

    public function exportUsers(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate([
            'format'     => 'required|in:xlsx,csv',
            'redact_pii' => 'nullable|boolean',
        ]);

        ExportLog::create([
            'user_id' => auth()->id(),
            'type'    => 'users',
            'format'  => $request->format,
            'filters' => ['redact_pii' => $request->boolean('redact_pii')],
            'status'  => 'completed',
        ]);

        $filename = 'users_export_' . now()->format('Ymd_His') . '.' . $request->format;

        return Excel::download(new UsersExport($request->boolean('redact_pii')), $filename);
    }

    public function exportMyData()
    {
        $user   = auth()->user();
        $orders = $user->orders()->with('orderItems.book')->get();

        $data = [
            'profile' => [
                'name'             => $user->name,
                'email'            => $user->email,
                'role'             => $user->role,
                'email_verified'   => $user->hasVerifiedEmail(),
                'two_factor'       => $user->two_factor_enabled,
                'member_since'     => $user->created_at?->toDateString(),
            ],
            'orders' => $orders->map(fn($o) => [
                'id'           => $o->id,
                'status'       => $o->status,
                'total'        => $o->total_amount,
                'date'         => $o->created_at?->toDateTimeString(),
                'items'        => $o->orderItems->map(fn($i) => [
                    'book'     => $i->book?->title,
                    'qty'      => $i->quantity,
                    'price'    => $i->unit_price,
                ]),
            ]),
            'exported_at' => now()->toIso8601String(),
        ];

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="my_data_' . now()->format('Ymd') . '.json"');
    }
}
