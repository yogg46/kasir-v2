<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\hargaModel;
use App\Models\cabangModel;
use App\Models\diskonModel;
use App\Models\produkModel;
use Livewire\WithPagination;
// use App\Models\discountsModels as diskonModel;
use App\Models\kategoriModel;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\DB;

class ProductList extends Component
{


    use WithPagination, WithFileUploads;

    // State Management
    public $showModal = false;
    public $modalMode = 'create'; // create | edit | view
    public $activeTab = 'info'; // info | prices | discounts

    // Product Data
    public $productId;
    public $category_id;
    public $name;
    public $description;
    public $barcode;
    public $type = 'regular';
    public $notes;
    public $is_active = true;

    // Prices Data (Multi-level)
    public $prices = [];
    public $newPrice = [
        'branch_id' => null,
        'unit_name' => '',
        'unit_qty' => 1,
        'price' => 0,
        'purchase_price' => 0,
        'is_default' => false,
        'valid_from' => null,
        'valid_until' => null,
        'notes' => ''
    ];

    // Discounts Data
    public $discounts = [];
    public $newDiscount = [
        'branch_id' => null,
        'type' => 'item',
        'discount_percent' => null,
        'discount_amount' => null,
        'valid_from' => null,
        'valid_until' => null,
        'notes' => ''
    ];

    // Filter & Search
    public $search = '';
    public $filterCategory = '';
    public $filterType = '';
    public $filterStatus = '';
    public $perPage = 10;

    // Collections
    public $categories;
    public $branches;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterType' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'category_id' => 'required|exists:categories_models,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|unique:products_models,barcode,' . $this->productId,
            'type' => 'required|in:umkm,regular,seasonal',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'category_id.required' => 'Kategori wajib dipilih.',
        'category_id.exists'   => 'Kategori yang dipilih tidak valid.',

        'name.required' => 'Nama produk wajib diisi.',
        'name.string'   => 'Nama produk harus berupa teks.',
        'name.max'      => 'Nama produk maksimal 255 karakter.',

        'description.string' => 'Deskripsi harus berupa teks.',

        'barcode.string' => 'Barcode harus berupa teks.',
        'barcode.unique' => 'Barcode sudah digunakan oleh produk lain.',

        'type.required' => 'Tipe produk wajib dipilih.',
        'type.in'       => 'Tipe produk harus salah satu dari: regular, umkm, atau seasonal.',

        'is_active.boolean' => 'Status harus bernilai aktif atau tidak aktif.',
    ];

    public function mount()
    {
        $this->categories = kategoriModel::all();
        $this->branches = cabangModel::all();
        $this->resetNewPrice();
        $this->resetNewDiscount();
    }

    public function render()
    {
        $products = produkModel::query()
            ->with(['toKategori', 'toHarga.toCabang', 'toStocks'])
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.master-data.product-list', [
            'products' => $products
        ]);
    }

    // ==========================================
    // CRUD OPERATIONS
    // ==========================================

    public function create()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
        $this->activeTab = 'info';
    }

    public function edit($id)
    {
        $product = produkModel::with(['toHarga', 'toHarga.toCabang'])->findOrFail($id);

        $this->productId = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->barcode = $product->barcode;
        $this->type = $product->type;
        $this->notes = $product->notes;
        $this->is_active = $product->is_active;

        // Load existing prices
        $this->prices = $product->toHarga->map(function ($price) {
            return [
                'id' => $price->id,
                'branch_id' => $price->branch_id,
                'branch_name' => $price->toCabang->name ?? 'Semua Cabang',
                'unit_name' => $price->unit_name,
                'unit_qty' => $price->unit_qty,
                'price' => $price->price,
                'purchase_price' => $price->purchase_price,
                'is_default' => $price->is_default,
                'valid_from' => $price->valid_from,
                'valid_until' => $price->valid_until,
                'notes' => $price->notes,
                'profit_margin' => $price->profit_margin
            ];
        })->toArray();

        // Load existing discounts
        $this->loadDiscounts($id);

        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function view($id)
    {
        $this->edit($id);
        $this->modalMode = 'view';
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            if ($this->modalMode === 'create') {
                $product = produkModel::create([
                    'category_id' => $this->category_id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'barcode' => $this->barcode,
                    'type' => $this->type,
                    'notes' => $this->notes,
                    'is_active' => $this->is_active,
                ]);

                $message = 'Produk berhasil ditambahkan';
            } else {

                $product = produkModel::findOrFail($this->productId);
                // dd('pp');
                $product->update([
                    'category_id' => $this->category_id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'barcode' => $this->barcode,
                    'type' => $this->type,
                    'notes' => $this->notes,
                    'is_active' => $this->is_active,
                ]);

                $message = 'Produk berhasil diupdate';
            }

            DB::commit();
            // $this->dispatch('notify', ['message' => $message, 'type' => 'success']);
            $this->dispatch('notify', message: $message, type: 'success');

            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: 'Gagal menyimpan: ' . $e->getMessage(),  type: 'error');
        }
    }

    public function delete($id)
    {
        try {
            $product = produkModel::findOrFail($id);
            $product->delete();

            $this->dispatch('notify', message: 'Produk berhasil dihapus', type: 'success');
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal menghapus: ' . $e->getMessage(), type: 'error');
        }
    }

    // ==========================================
    // PRICE MANAGEMENT
    // ==========================================

    public function addPrice()
    {
        $this->validate([
            'newPrice.branch_id' => 'required',
            'newPrice.unit_name' => 'required|string|max:50',
            'newPrice.unit_qty' => 'required|integer|min:1',
            'newPrice.price' => 'required|numeric|min:0',
            'newPrice.purchase_price' => 'nullable|numeric|min:0',
        ]);

        // Tambahkan manual validasi khusus:
        if ($this->newPrice['branch_id'] !== 'all' && !is_numeric($this->newPrice['branch_id'])) {
            // $this->addError('newPrice.branch_id', 'Cabang tidak valid.');
            $this->dispatch('notify', message: 'Cabang tidak valid.', type: 'error');
            return;
        }

        if ($this->newPrice['purchase_price'] && $this->newPrice['price'] < $this->newPrice['purchase_price']) {
            $this->dispatch('notify', message: 'Harga jual tidak boleh lebih kecil dari harga beli', type: 'error');
            return;
        }

        try {
            // Jika produk sudah tersimpan di database
            if ($this->productId) {
                // Jika pilih "Semua Cabang"
                if ($this->newPrice['branch_id'] === 'all') {
                    foreach ($this->branches as $branch) {
                        hargaModel::create([
                            'product_id' => $this->productId,
                            'branch_id' => $branch->id,
                            'unit_name' => $this->newPrice['unit_name'],
                            'unit_qty' => $this->newPrice['unit_qty'],
                            'price' => $this->newPrice['price'],
                            'purchase_price' => $this->newPrice['purchase_price'],
                            'is_default' => $this->newPrice['is_default'] ?? false,
                            'valid_from' => $this->newPrice['valid_from'] ?? null,
                            'valid_until' => $this->newPrice['valid_until'] ?? null,
                            'notes' => $this->newPrice['notes'] ?? null,
                        ]);
                    }
                } else {
                    // Jika hanya 1 cabang
                    hargaModel::create([
                        'product_id' => $this->productId,
                        'branch_id' => $this->newPrice['branch_id'],
                        'unit_name' => $this->newPrice['unit_name'],
                        'unit_qty' => $this->newPrice['unit_qty'],
                        'price' => $this->newPrice['price'],
                        'purchase_price' => $this->newPrice['purchase_price'],
                        'is_default' => $this->newPrice['is_default'] ?? false,
                        'valid_from' => $this->newPrice['valid_from'] ?? null,
                        'valid_until' => $this->newPrice['valid_until'] ?? null,
                        'notes' => $this->newPrice['notes'] ?? null,
                    ]);
                }

                // Refresh daftar harga
                $this->loadPrices($this->productId);

                $this->dispatch('notify', message: 'Harga berhasil ditambahkan', type: 'success');
            }
            // Jika produk belum tersimpan (mode tambah produk)
            else {
                if ($this->newPrice['branch_id'] === 'all') {
                    foreach ($this->branches as $branch) {
                        $this->prices[] = [
                            'id' => 'temp_' . count($this->prices),
                            'branch_id' => $branch->id,
                            'branch_name' => $branch->name,
                            'unit_name' => $this->newPrice['unit_name'],
                            'unit_qty' => $this->newPrice['unit_qty'],
                            'price' => $this->newPrice['price'],
                            'purchase_price' => $this->newPrice['purchase_price'],
                            'profit_margin' => $this->calculateProfitMargin(
                                $this->newPrice['price'],
                                $this->newPrice['purchase_price']
                            ),
                        ];
                    }
                } else {
                    $branch = $this->branches->find($this->newPrice['branch_id']);
                    $this->prices[] = [
                        'id' => 'temp_' . count($this->prices),
                        'branch_id' => $this->newPrice['branch_id'],
                        'branch_name' => $branch ? $branch->name : 'Semua Cabang',
                        'unit_name' => $this->newPrice['unit_name'],
                        'unit_qty' => $this->newPrice['unit_qty'],
                        'price' => $this->newPrice['price'],
                        'purchase_price' => $this->newPrice['purchase_price'],
                        'profit_margin' => $this->calculateProfitMargin(
                            $this->newPrice['price'],
                            $this->newPrice['purchase_price']
                        ),
                    ];
                }
            }

            $this->resetNewPrice();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal menambah harga: ' . $e->getMessage(), type: 'error');
        }
    }

    public function deletePrice($index, $id = null)
    {
        if ($id && !str_starts_with($id, 'temp_')) {
            try {
                hargaModel::findOrFail($id)->delete();
                $this->dispatch('notify', message: 'Harga berhasil dihapus', type: 'success');
            } catch (\Exception $e) {
                $this->dispatch('notify', message: 'Gagal menghapus harga: ' . $e->getMessage(), type: 'error');
                return;
            }
        }

        unset($this->prices[$index]);
        $this->prices = array_values($this->prices);
    }

    public function setDefaultPrice($index, $id = null)
    {
        if ($id && !str_starts_with($id, 'temp_')) {
            try {
                $price = hargaModel::findOrFail($id);

                // Unset default lainnya untuk produk & cabang yang sama
                hargaModel::where('product_id', $price->product_id)
                    ->where('branch_id', $price->branch_id)
                    ->update(['is_default' => false]);

                $price->update(['is_default' => true]);
                $this->loadPrices($this->productId);
                $this->dispatch('notify', message: 'Harga default berhasil diubah', type: 'success');
            } catch (\Exception $e) {
                $this->dispatch('notify', message: 'Gagal mengubah default: ' . $e->getMessage(), type: 'error');
            }
        } else {
            // Update array sementara
            foreach ($this->prices as $key => $price) {
                if ($price['branch_id'] == $this->prices[$index]['branch_id']) {
                    $this->prices[$key]['is_default'] = ($key == $index);
                }
            }
        }
    }

    // ==========================================
    // DISCOUNT MANAGEMENT
    // ==========================================

    public function addDiscount()
    {
        $this->validate([
            'newDiscount.type' => 'required|in:item,transaction',
            'newDiscount.discount_percent' => 'nullable|numeric|min:0|max:100',
            'newDiscount.discount_amount' => 'nullable|numeric|min:0',
        ]);

        if (!$this->newDiscount['discount_percent'] && !$this->newDiscount['discount_amount']) {
            $this->dispatch('notify',  message: 'Isi salah satu: persen atau nominal', type: 'error');
            return;
        }

        try {
            if ($this->productId) {
                diskonModel::create([
                    'product_id' => $this->productId,
                    'branch_id' => $this->newDiscount['branch_id'],
                    'type' => $this->newDiscount['type'],
                    'discount_percent' => $this->newDiscount['discount_percent'],
                    'discount_amount' => $this->newDiscount['discount_amount'],
                    'valid_from' => $this->newDiscount['valid_from'],
                    'valid_until' => $this->newDiscount['valid_until'],
                    'notes' => $this->newDiscount['notes'],
                ]);

                $this->loadDiscounts($this->productId);
                $this->dispatch('notify', message: 'Diskon berhasil ditambahkan', type: 'success');
            } else {
                // Tambah ke array sementara
                $branch = $this->branches->find($this->newDiscount['branch_id']);
                $this->discounts[] = array_merge($this->newDiscount, [
                    'id' => 'temp_' . count($this->discounts),
                    'branch_name' => $branch ? $branch->name : 'Semua Cabang'
                ]);
            }

            $this->resetNewDiscount();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal menambah diskon: ' . $e->getMessage(), type: 'error');
        }
    }

    public function deleteDiscount($index, $id = null)
    {
        if ($id && !str_starts_with($id, 'temp_')) {
            try {
                diskonModel::findOrFail($id)->delete();
                $this->dispatch('notify', message: 'Diskon berhasil dihapus', type: 'success');
            } catch (\Exception $e) {
                $this->dispatch('notify',  message: 'Gagal menghapus diskon: ' . $e->getMessage(), type: 'error');
                return;
            }
        }

        unset($this->discounts[$index]);
        $this->discounts = array_values($this->discounts);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function loadPrices($productId)
    {
        $product = produkModel::with(['toHarga.toCabang'])->find($productId);
        $this->prices = $product->toHarga->map(function ($price) {
            return [
                'id' => $price->id,
                'branch_id' => $price->branch_id,
                'branch_name' => $price->toCabang->name ?? 'Semua Cabang',
                'unit_name' => $price->unit_name,
                'unit_qty' => $price->unit_qty,
                'price' => $price->price,
                'purchase_price' => $price->purchase_price,
                'is_default' => $price->is_default,
                'valid_from' => $price->valid_from,
                'valid_until' => $price->valid_until,
                'notes' => $price->notes,
                'profit_margin' => $price->profit_margin
            ];
        })->toArray();
    }

    private function loadDiscounts($productId)
    {
        $discounts = diskonModel::where('product_id', $productId)
            ->with('toCabang')
            ->get();

        $this->discounts = $discounts->map(function ($discount) {
            return [
                'id' => $discount->id,
                'branch_id' => $discount->branch_id,
                'branch_name' => $discount->toCabang->name ?? 'Semua Cabang',
                'type' => $discount->type,
                'discount_percent' => $discount->discount_percent,
                'discount_amount' => $discount->discount_amount,
                'valid_from' => $discount->valid_from,
                'valid_until' => $discount->valid_until,
                'notes' => $discount->notes
            ];
        })->toArray();
    }

    private function calculateProfitMargin($price, $purchasePrice)
    {
        if (!$purchasePrice || $purchasePrice == 0) return 0;
        return round((($price - $purchasePrice) / $purchasePrice) * 100, 2);
    }

    public function resetForm()
    {
        $this->reset([
            'productId',
            'category_id',
            'name',
            'description',
            'barcode',
            'type',
            'notes',
            'is_active',
            'prices',
            'discounts'
        ]);
        $this->resetNewPrice();
        $this->resetNewDiscount();
    }

    private function resetNewPrice()
    {
        $this->newPrice = [
            'branch_id' => null,
            'unit_name' => '',
            'unit_qty' => 1,
            'price' => 0,
            'purchase_price' => 0,
            'is_default' => false,
            'valid_from' => null,
            'valid_until' => null,
            'notes' => ''
        ];
    }

    private function resetNewDiscount()
    {
        $this->newDiscount = [
            'branch_id' => null,
            'type' => 'item',
            'discount_percent' => null,
            'discount_amount' => null,
            'valid_from' => null,
            'valid_until' => null,
            'notes' => ''
        ];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }
}
