<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\{
    productsModels as Product,
    categoriesModels as Category,
    pricesModels as Price,
    stockModels as Stock,
    branchesModel as Branch,
    warehosesModels as Warehouse,
    salesModels as Sale,
    saleitemsModels as SaleItem
};

class POS extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $barcode = '';
    public string $activeCategory = 'all';
    public int $perPage = 12;

    // Context (branch & warehouse)
    public $activeBranch;
    public $activeWarehouse;

    // Cart
    public array $cart = [];

    // Checkout
    public bool $showCheckout = false;
    public string $customerName = 'Umum';
    public float $paymentAmount = 0;
    public string $paymentMethod = '';

    protected $listeners = ['productAdded' => 'addToCart'];

    public function mount()
    {
        $this->cart = session('cart', []);

        // set default branch & warehouse (cari head office dulu)
        $this->activeBranch = Branch::where('is_head_office', true)->first() ?? Branch::first();
        $this->activeWarehouse = $this->activeBranch
            ? $this->activeBranch->toWarehouses()->where('is_main', true)->first() ?? $this->activeBranch->toWarehouses()->first()
            : null;
    }

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::query()
            ->with([
                'toCategory',
                'toSupplier',
                'toPrices' => function ($q) {
                    if ($this->activeBranch) {
                        $q->where('branch_id', $this->activeBranch->id);
                    }
                }
            ])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('barcode', 'like', "%{$this->search}%");
                });
            })
            ->when($this->barcode, fn($q) => $q->where('barcode', $this->barcode))
            ->when($this->activeCategory !== 'all', fn($q) => $q->where('category_id', $this->activeCategory))
            ->where('is_active', true)
            ->paginate($this->perPage);

        return view('livewire.sales.p-o-s', [
            'products'   => $products,
            'categories' => $categories,
            'total'      => $this->getTotal(),
            'change'     => $this->getChange(),
        ])->layout('components.layouts.app-kasir');
    }

    // ---------------------------
    // Filters / barcode
    // ---------------------------
    public function setCategory($categoryId)
    {
        $this->activeCategory = $categoryId;
        $this->resetPage();
    }

    public function updatedBarcode($value)
    {
        if (! $value) return;

        $product = Product::where('barcode', $value)->first();
        if ($product) {
            $this->addToCart($product->id);
            $this->barcode = '';
        } else {
            $this->flashError('Produk tidak ditemukan!');
        }
    }

    // ---------------------------
    // Cart management
    // ---------------------------
    public function addToCart($productId)
    {
        // load product with price (branch) and stock (warehouse)
        $product = Product::with([
            'toPrices' => function ($q) {
                if ($this->activeBranch) {
                    $q->where('branch_id', $this->activeBranch->id)->where('is_default', true);
                }
            },
            'toStocks' => function ($q) {
                if ($this->activeWarehouse) {
                    $q->where('warehouse_id', $this->activeWarehouse->id);
                }
            }
        ])->find($productId);

        if (! $product) {
            $this->flashError('Produk tidak ditemukan!');
            return;
        }

        $price = $product->toPrices->first()?->price ?? 0;
        $stockQty = $product->toStocks->first()?->quantity ?? 0;

        if ($stockQty <= 0) {
            $this->flashError('Stok produk habis!');
            return;
        }

        $existingIndex = array_search($product->id, array_column($this->cart, 'id'));

        if ($existingIndex !== false) {
            // already in cart
            if ($this->cart[$existingIndex]['quantity'] < $stockQty) {
                $this->cart[$existingIndex]['quantity']++;
            } else {
                $this->flashError('Stok tidak mencukupi!');
                return;
            }
        } else {
            // add new item
            $this->cart[] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $price,
                'quantity' => 1,
                'stock'    => $stockQty,
                'unit'     => $product->toPrices->first()?->unit_name ?? null
            ];
        }

        $this->saveCart();
        $this->flashSuccess('Produk ditambahkan ke keranjang!');
    }

    public function updateQuantity(int $index, string $action)
    {
        if (! isset($this->cart[$index])) return;

        if ($action === 'increment') {
            $this->incrementQuantity($index);
        } elseif ($action === 'decrement') {
            $this->decrementQuantity($index);
        }

        $this->saveCart();
    }

    protected function incrementQuantity(int $index)
    {
        if ($this->cart[$index]['quantity'] < $this->cart[$index]['stock']) {
            $this->cart[$index]['quantity']++;
        } else {
            $this->flashError('Stok tidak mencukupi!');
        }
    }

    protected function decrementQuantity(int $index)
    {
        if ($this->cart[$index]['quantity'] > 1) {
            $this->cart[$index]['quantity']--;
        } else {
            $this->removeFromCart($index);
        }
    }

    public function removeFromCart(int $index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // reindex
            $this->saveCart();
            $this->flashSuccess('Produk dihapus dari keranjang!');
        }
    }

    public function resetCart()
    {
        $this->cart = [];
        $this->saveCart();
        $this->flashSuccess('Keranjang dikosongkan!');
    }

    protected function saveCart()
    {
        session()->put('cart', $this->cart);
    }

    // ---------------------------
    // Checkout & payment
    // ---------------------------
    public function openCheckout()
    {
        if (empty($this->cart)) {
            $this->flashError('Keranjang kosong!');
            return;
        }
        $this->showCheckout = true;
    }

    public function closeCheckout()
    {
        $this->showCheckout = false;
    }

    public function setPaymentAmount($amount)
    {
        $this->paymentAmount = (float) $amount;
        $this->dispatch('refreshPaymentAmount');
    }

    public function processPayment()
    {
        // validate
        $this->validate([
            'customerName' => 'required|string|max:255',
            'paymentAmount' => 'required|numeric|min:' . $this->getTotal(),
            'paymentMethod' => 'required|string'
        ], [
            'paymentAmount.min' => 'Nominal pembayaran kurang!',
            'paymentMethod.required' => 'Pilih metode pembayaran!'
        ]);

        DB::beginTransaction();

        try {
            // create sale header (salesModels)
            $sale = Sale::create([
                'branch_id'     => $this->activeBranch?->id,
                'cashier_id'    => auth()->id(),
                'sale_date'     => now(),
                'subtotal'      => $this->getTotal(),
                'total_amount'  => $this->getTotal(),
                'discount_total'=> 0,
                'payment_method'=> $this->paymentMethod,
                'status'        => 'paid',
                'notes'         => 'customer: ' . $this->customerName . ' | paid: ' . number_format($this->paymentAmount,0,',','.') . ' | change: ' . number_format($this->getChange(),0,',','.')
            ]);

            // create items and decrement stock per warehouse
            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'unit_name'  => $item['unit'] ?? null,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'discount'   => 0,
                    'subtotal'   => $item['price'] * $item['quantity'],
                ]);

                // update stockModels for active warehouse
                if ($this->activeWarehouse) {
                    $stock = Stock::where('product_id', $item['id'])
                        ->where('warehouse_id', $this->activeWarehouse->id)
                        ->first();

                    if ($stock) {
                        // decrement quantity safely
                        $decrement = min($stock->quantity, $item['quantity']);
                        $stock->decrement('quantity', $decrement);
                    }
                } else {
                    // fallback: if no warehouse active, try global stock decrement on product (if you have column)
                    if (isset($item['stock'])) {
                        // no-op here; adapt if you keep global stock on products
                    }
                }
            }

            DB::commit();

            $this->flashSuccess('Transaksi berhasil! Kembalian: Rp ' . number_format($this->getChange(), 0, ',', '.'));

            // reset cart & checkout states
            $this->resetCart();
            $this->reset(['showCheckout', 'customerName', 'paymentAmount', 'paymentMethod']);

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->flashError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ---------------------------
    // Helpers
    // ---------------------------
    public function getTotal(): float
    {
        return collect($this->cart)->sum(fn($i) => $i['price'] * $i['quantity']);
    }

    public function getChange(): float
    {
        return max(0, $this->paymentAmount - $this->getTotal());
    }

    private function flashSuccess(string $msg)
    {
        session()->flash('success', $msg);
    }

    private function flashError(string $msg)
    {
        session()->flash('error', $msg);
    }
}
