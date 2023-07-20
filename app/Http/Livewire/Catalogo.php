<?php

namespace App\Http\Livewire;

use App\Models\Color;
use App\Models\GlobalAttribute;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Type;
use Illuminate\Support\Facades\DB;

class Catalogo extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $proveedores;

    public $nombre, $sku, $proveedor, $color, $category, $type, $precioMax, $precioMin, $stockMax, $stockMin, $orderStock = '', $orderPrice = '';

    public function __construct()
    {
        $stock = DB::table('products')->max('stock');
        $this->stockMax = $stock;
        $this->stockMin = 0;
    }

    public function render()
    {
        // Agrupar Colores similares
        $types = Type::find([1,2]);
        $stock = DB::table('products')->max('stock');

        $nombre = '%' . $this->nombre . '%';
        $sku = '%' . $this->sku . '%';
        $color = $this->color;
        $category = $this->category;
        $type =  $this->type == "" ? null : $this->type;
        $stockMax =  $this->stockMax;
        $stockMin =  $this->stockMin;
        if ($stockMax == null) {
            $stockMax = $stock;
        }
        if ($stockMin == null) {
            $stockMin = 0;
        }

        $orderStock = $this->orderStock;

        $products  = Product::leftjoin('product_category', 'product_category.product_id', 'products.id')
            ->leftjoin('categories', 'product_category.category_id', 'categories.id')
            ->leftjoin('colors', 'products.color_id', 'colors.id')
            ->where('products.name', 'LIKE', $nombre)
            ->where('products.visible', '=', true)
            ->where('products.sku', 'LIKE', $sku)
            ->whereBetween('products.stock', [$stockMin, $stockMax])
            ->where('products.provider_id', '<', 14)
            ->where('products.type_id', 'LIKE', $type)
            ->when($orderStock !== '', function ($query, $orderStock) {
                $query->orderBy('products.stock', $this->orderStock);
            })
            ->when($color !== '' && $color !== null, function ($query, $color) {
                $newColor  = '%' . $this->color . '%';
                $query->where('colors.color', 'LIKE', $newColor);
            })
            ->when($category !== '' && $category !== null, function ($query, $category) {
                $newCat  = '%' . $this->category . '%';
                $query->where('categories.family', 'LIKE', $newCat);
            })
            ->select('products.*')
            ->paginate(32);

        return view('cotizador.catalogo.view', [
            'products' => $products,
            'types' => $types,
            'stock' => $stock,
            'stockMax' => $stockMax,
            'stockMin' => $stockMin,
            'orderStock' => $orderStock,
        ]);
    }

    public function showProduct(Product $product)
    {
        $this->emit('showProductListener', $product->id);
    }

    public function limpiar()
    {
        $this->nombre = '';
        $this->sku = '';
        $this->color = '';
        $this->category = '';
        $this->proveedor = null;
        $this->type = null;
        $this->orderPrice = '';
        $this->orderStock = '';
    }
}
