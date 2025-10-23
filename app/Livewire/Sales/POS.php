<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\{
    produkModel as Product,
    kategoriModel as Category,
    hargaModel as Price,
    stockModels as Stock,
    cabangModel as Branch,
    gudangModel as Warehouse,
    salesModels as Sale,
    saleitemsModels as SaleItem
};
use Illuminate\Support\Facades\Auth;

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

    // Price Selection Modal
    public bool $showPriceModal = false;
    public $selectedProduct = null;
    public array $availablePrices = [];

    // Checkout
    public bool $showCheckout = false;
    public string $customerName = 'Umum';
    public float $paymentAmount = 0;
    public string $paymentMethod = 'cash';

    protected $listeners = ['productAdded' => 'addToCart'];

    public function mount()
    {
        $this->cart = session('cart', []);

        // set default branch & warehouse (cari head office dulu)
        $this->activeBranch = Branch::where('is_head_office', true)->first() ?? Branch::first();
        $this->activeWarehouse = $this->activeBranch
            ? $this->activeBranch->toGudang()->where('is_main', true)->first() ?? $this->activeBranch->toGudang()->first()
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
                'toKategori',
                'toHarga' => function ($q) {
                    if ($this->activeBranch) {
                        $q->where('branch_id', $this->activeBranch->id)
                            ->where(function ($query) {
                                $query->whereNull('valid_from')
                                    ->orWhere('valid_from', '<=', now());
                            })
                            ->where(function ($query) {
                                $query->whereNull('valid_until')
                                    ->orWhere('valid_until', '>=', now());
                            })
                            ->orderBy('unit_qty', 'asc'); // urutkan dari qty terkecil
                    }
                },
                'toStocks' => function ($q) {
                    if ($this->activeWarehouse) {
                        $q->where('warehouse_id', $this->activeWarehouse->id);
                    }
                }
            ])
            ->search($this->search)
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
            $this->openPriceSelection($product->id);
            $this->barcode = '';
        } else {
            $this->flashError('Produk tidak ditemukan!');
        }
    }

    // ---------------------------
    // Price Selection Modal
    // ---------------------------
    public function openPriceSelection($productId)
    {
        $this->selectedProduct = Product::with([
            'toHarga' => function ($q) {
                if ($this->activeBranch) {
                    $q->where('branch_id', $this->activeBranch->id)
                        ->where(function ($query) {
                            $query->whereNull('valid_from')
                                ->orWhere('valid_from', '<=', now());
                        })
                        ->where(function ($query) {
                            $query->whereNull('valid_until')
                                ->orWhere('valid_until', '>=', now());
                        })
                        ->orderBy('unit_qty', 'asc');
                }
            },
            'toStocks' => function ($q) {
                if ($this->activeWarehouse) {
                    $q->where('warehouse_id', $this->activeWarehouse->id);
                }
            }
        ])->find($productId);

        if (!$this->selectedProduct) {
            $this->flashError('Produk tidak ditemukan!');
            return;
        }

        $stockQty = $this->selectedProduct->toStocks->first()?->quantity ?? 0;

        if ($stockQty <= 0) {
            $this->flashError('Stok produk habis!');
            return;
        }

        // Ambil semua tier harga yang tersedia
        $this->availablePrices = $this->selectedProduct->toHarga->map(function ($price) use ($stockQty) {
            $unitQty = $price->unit_qty ?? 1;
            return [
                'id' => $price->id,
                'unit_name' => $price->unit_name ?? 'Pcs',
                'unit_qty' => $unitQty,
                'price' => $price->price,
                'old_price' => $price->old_price,
                'is_default' => $price->is_default ?? false,
                'available' => $stockQty >= $unitQty,
                'notes' => $price->notes
            ];
        })->toArray();

        // Jika tidak ada harga, tampilkan error
        if (count($this->availablePrices) === 0) {
            $this->flashError('Produk belum memiliki harga!');
            return;
        }

        // Jika hanya ada 1 harga, langsung add to cart
        if (count($this->availablePrices) === 1) {
            $this->addToCartWithPrice(0);
            return;
        }

        $this->showPriceModal = true;
    }

    public function closePriceModal()
    {
        $this->showPriceModal = false;
        $this->selectedProduct = null;
        $this->availablePrices = [];
    }

    public function addToCartWithPrice($priceIndex)
    {
        if (!isset($this->availablePrices[$priceIndex])) {
            $this->flashError('Harga tidak valid!');
            return;
        }

        $selectedPrice = $this->availablePrices[$priceIndex];
        $stockQty = $this->selectedProduct->toStocks->first()?->quantity ?? 0;

        // Validasi stok untuk unit_qty
        if ($stockQty < $selectedPrice['unit_qty']) {
            $this->flashError('Stok tidak mencukupi untuk harga ini!');
            return;
        }

        // Cek apakah produk dengan tier yang sama sudah ada di cart
        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            if (
                $item['id'] === $this->selectedProduct->id &&
                $item['price_id'] === $selectedPrice['id']
            ) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Update quantity dengan kelipatan unit_qty
            $newQty = $this->cart[$existingIndex]['quantity'] + $selectedPrice['unit_qty'];
            if ($newQty <= $stockQty) {
                $this->cart[$existingIndex]['quantity'] = $newQty;
            } else {
                $this->flashError('Stok tidak mencukupi!');
                $this->closePriceModal();
                return;
            }
        } else {
            // Add new item dengan quantity = unit_qty
            $this->cart[] = [
                'id'         => $this->selectedProduct->id,
                'price_id'   => $selectedPrice['id'],
                'name'       => $this->selectedProduct->name,
                'price'      => $selectedPrice['price'],
                'quantity'   => $selectedPrice['unit_qty'],
                'unit_qty'   => $selectedPrice['unit_qty'],
                'stock'      => $stockQty,
                'unit'       => $selectedPrice['unit_name'],
                'tier_label' => $this->formatTierLabel($selectedPrice)
            ];
        }

        $this->saveCart();
        $this->flashSuccess('✓ ' . $this->selectedProduct->name . ' ditambahkan (' . $this->formatTierLabel($selectedPrice) . ')');
        $this->closePriceModal();
    }

    private function formatTierLabel($price)
    {
        if ($price['unit_qty'] == 1) {
            return $price['unit_name'];
        }
        return $price['unit_name'] . ' (' . $price['unit_qty'] . ' item)';
    }

    // ---------------------------
    // Cart management
    // ---------------------------
    public function addToCart($productId)
    {
        $this->openPriceSelection($productId);
    }

    public function updateQuantity(int $index, string $action)
    {
        if (! isset($this->cart[$index])) return;

        $unitQty = $this->cart[$index]['unit_qty'] ?? 1;

        if ($action === 'increment') {
            $newQty = $this->cart[$index]['quantity'] + $unitQty;
            if ($newQty <= $this->cart[$index]['stock']) {
                $this->cart[$index]['quantity'] = $newQty;
            } else {
                $this->flashError('Stok tidak mencukupi!');
                return;
            }
        } elseif ($action === 'decrement') {
            $newQty = $this->cart[$index]['quantity'] - $unitQty;
            if ($newQty >= $unitQty) {
                $this->cart[$index]['quantity'] = $newQty;
            } else {
                $this->removeFromCart($index);
                return;
            }
        }

        $this->saveCart();
    }

    public function removeFromCart(int $index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
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

    public function updatePaymentMethod()
    {
        if ($this->paymentMethod == 'qris') {
            $this->paymentAmount = $this->getTotal();
            $this->dispatch('refreshPaymentAmount');
        }
    }

    public function processPayment()
    {
        // validate

        $this->validate([
            // 'customerName' => 'required|string|max:255',
            'paymentAmount' => 'required|numeric|min:' . $this->getTotal(),
            'paymentMethod' => 'required|string'
        ], [
            'paymentAmount.min' => 'Nominal pembayaran kurang!',
            'paymentMethod.required' => 'Pilih metode pembayaran!'
        ]);


        DB::beginTransaction();

        try {
            // create sale header
            $sale = Sale::create([
                'branch_id'      => $this->activeBranch?->id,
                'cashier_id'     => Auth::user()->id,
                'sale_date'      => now(),
                'subtotal'       => $this->getTotal(),
                'total_amount'   => $this->getTotal(),
                'discount_total' => 0,
                'payment_method' => $this->paymentMethod,
                'status'         => 'paid',
                'notes'          => 'Customer: ' . ' | Bayar: Rp ' . number_format($this->paymentAmount, 0, ',', '.') . ' | Kembalian: Rp ' . number_format($this->getChange(), 0, ',', '.')
            ]);

            // create items and decrement stock
            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'unit_name'  => $item['tier_label'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'discount'   => 0,
                    'subtotal'   => $item['price'] * $item['quantity'],
                ]);

                // update stock
                if ($this->activeWarehouse) {
                    $stock = Stock::where('product_id', $item['id'])
                        ->where('warehouse_id', $this->activeWarehouse->id)
                        ->first();

                    if ($stock) {
                        $decrement = min($stock->quantity, $item['quantity']);
                        $stock->decrement('quantity', $decrement);
                    }
                }
            }

            DB::commit();

            $this->flashSuccess('✓ Transaksi berhasil! Kembalian: Rp ' . number_format($this->getChange(), 0, ',', '.'));

            // reset
            $this->closeCheckout();
            $this->resetCart();
            $this->reset(['showCheckout', 'customerName', 'paymentAmount', 'paymentMethod']);
        } catch (\Throwable $e) {
            $this->closeCheckout();
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
