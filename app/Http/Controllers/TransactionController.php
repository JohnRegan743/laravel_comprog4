<?php

namespace App\Http\Controllers;

use App\Mail\TransactionReceipt;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    public function index()
    {
        $transactions = Transaction::with('user')
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $customers = User::where('role', 'Customer')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $products = Product::where('active', true)
            ->orderBy('name')
            ->get();

        return view('transactions.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'nullable|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'nullable|integer|min:1',
        ]);

        $productIds = $request->input('product_id', []);
        $quantities = $request->input('quantity', []);

        $lineItems = [];
        foreach ($productIds as $index => $productId) {
            if (!$productId || !isset($quantities[$index]) || (int) $quantities[$index] < 1) {
                continue;
            }

            $lineItems[] = [
                'product_id' => (int) $productId,
                'quantity' => (int) $quantities[$index],
            ];
        }

        if (empty($lineItems)) {
            return back()
                ->withInput()
                ->withErrors(['product_id' => 'Please add at least one valid product with quantity.']);
        }

        $transaction = DB::transaction(function () use ($request, $lineItems) {
            $transaction = Transaction::create([
                'user_id' => $request->integer('user_id'),
                'transaction_number' => $this->generateTransactionNumber(),
                'total_amount' => 0,
                'status' => $request->input('status', 'completed'),
                'notes' => $request->input('notes'),
            ]);

            $total = 0;

            foreach ($lineItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unitPrice = $product->unit_price;
                $lineTotal = $unitPrice * $item['quantity'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ]);

                $total += $lineTotal;
            }

            $transaction->update([
                'total_amount' => $total,
            ]);

            return $transaction->fresh(['user', 'items.product']);
        });

        $this->sendReceiptIfCompleted($transaction, true);

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'items.product']);

        $statusOptions = ['pending', 'processing', 'completed', 'cancelled'];

        return view('transactions.show', compact('transaction', 'statusOptions'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $previousStatus = $transaction->status;
        $transaction->update($data);

        $transaction->load(['user', 'items.product']);

        if ($previousStatus !== $transaction->status) {
            $this->sendReceiptIfCompleted($transaction);
        }

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Transaction status updated successfully.');
    }

    public function resendReceipt(Transaction $transaction)
    {
        $transaction->load(['user', 'items.product']);

        $this->sendReceiptIfCompleted($transaction, true);

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Receipt email has been resent.');
    }

    protected function generateTransactionNumber(): string
    {
        return 'TX-' . now()->format('YmdHis') . '-' . mt_rand(100, 999);
    }

    protected function sendReceiptIfCompleted(Transaction $transaction, bool $force = false): void
    {
        if ($transaction->status !== 'completed' && !$force) {
            return;
        }

        if (!$transaction->relationLoaded('user')) {
            $transaction->load('user');
        }

        if (!$transaction->user) {
            return;
        }

        Mail::to($transaction->user->email)
            ->send(new TransactionReceipt($transaction));
    }
}

