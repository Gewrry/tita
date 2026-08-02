<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->where('track_stock', true)
                    ->whereColumn('stock_quantity', '<=', 'reorder_level')
                    ->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out') {
                $query->where('track_stock', true)->where('stock_quantity', '<=', 0);
            }
        }

        // Sorting
        match ($request->sort) {
            'name_asc'    => $query->orderBy('name', 'asc'),
            'name_desc'   => $query->orderBy('name', 'desc'),
            'price_asc'   => $query->orderBy('selling_price', 'asc'),
            'price_desc'  => $query->orderBy('selling_price', 'desc'),
            'stock_asc'   => $query->orderBy('stock_quantity', 'asc'),
            'stock_desc'  => $query->orderBy('stock_quantity', 'desc'),
            default       => $query->latest(),
        };

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();
        $units = Product::$units;
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0.01',
            'selling_price' => 'required|numeric|min:0.01',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'required|in:' . implode(',', array_keys(Product::$units)),
            'is_active' => 'nullable|boolean',
            'track_stock' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['track_stock'] = $request->boolean('track_stock', true);
        $validated['reorder_level'] = $validated['reorder_level'] ?? 5;

        if ($request->hasFile('image')) {
            try {
                $response = cloudinary()->uploadApi()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'tita_products'
                ]);
                $validated['image_path'] = $response['secure_url'];
            } catch (\Throwable $e) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Image upload failed: ' . $e->getMessage(),
                        'errors' => ['image' => ['Image upload failed: ' . $e->getMessage()]]
                    ], 422);
                }
                return back()->withErrors(['image' => 'Image upload failed: ' . $e->getMessage()])->withInput();
            }
        }
        unset($validated['image']);

        $product = Product::create($validated);

        // Record initial stock
        if ($product->stock_quantity > 0) {
            $product->adjustStock(0, 'stock_in', null, null, 'Initial stock on product creation');
            // Fix: the adjustStock added 0, let's record correctly
            $product->stockMovements()->first()->update([
                'quantity' => $product->stock_quantity,
                'stock_before' => 0,
                'stock_after' => $product->stock_quantity,
            ]);
        }

        AuditTrail::log('created', $product, null, $product->toArray());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => $product,
                'redirect' => route('products.index')
            ], 201);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->get();
        $units = Product::$units;
        $product->load('stockMovements');
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0.01',
            'selling_price' => 'required|numeric|min:0.01',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'required|in:' . implode(',', array_keys(Product::$units)),
            'is_active' => 'nullable|boolean',
            'track_stock' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $oldValues = $product->toArray();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['track_stock'] = $request->boolean('track_stock', true);

        if ($request->hasFile('image')) {
            try {
                $response = cloudinary()->uploadApi()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'tita_products'
                ]);
                $validated['image_path'] = $response['secure_url'];
            } catch (\Throwable $e) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Image upload failed: ' . $e->getMessage(),
                        'errors' => ['image' => ['Image upload failed: ' . $e->getMessage()]]
                    ], 422);
                }
                return back()->withErrors(['image' => 'Image upload failed: ' . $e->getMessage()])->withInput();
            }
        }
        unset($validated['image']);

        $product->update($validated);
        AuditTrail::log('updated', $product, $oldValues, $product->fresh()->toArray());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        AuditTrail::log('deleted', $product, $product->toArray());
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Adjust stock (stock in, adjustment, spoilage)
     */
    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'type' => 'required|in:stock_in,adjustment,spoilage,return',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        $qty = $validated['type'] === 'spoilage' 
            ? -abs($validated['quantity']) 
            : $validated['quantity'];

        $movement = $product->adjustStock($qty, $validated['type'], null, null, $validated['notes']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Stock adjusted: {$validated['type']} {$validated['quantity']} units.",
                'new_stock' => $product->fresh()->stock_quantity,
                'movement' => [
                    'type' => $movement->type,
                    'quantity' => $movement->quantity,
                    'stock_before' => $movement->stock_before,
                    'stock_after' => $movement->stock_after,
                    'notes' => $movement->notes,
                    'date' => $movement->created_at->format('M d, Y H:i')
                ]
            ]);
        }

        return redirect()->back()
            ->with('success', "Stock adjusted: {$validated['type']} {$validated['quantity']} units.");
    }
}
