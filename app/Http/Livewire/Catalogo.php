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

    public $nombre, $sku, $proveedor, $color, $category, $type, $precioMax, $precioMin, $stockMax, $stockMin, $orderStock = '', $orderPrice = '';

    public function __construct()
    {
        $utilidad = GlobalAttribute::find(1);
        $utilidad = 50;
        $price = DB::table('products')->max('price');
        $this->precioMax = round($price + $price * ($utilidad / 100), 2);
        $this->precioMin = 0;
        $stock = DB::table('products')->max('stock');
        $this->stockMax = $stock;
        $this->stockMin = 0;
    }

    public function render()
    {
        $utilidad = GlobalAttribute::find(1);
        $utilidad = (float) $utilidad->value;

        $proveedores = Provider::all();
        // Agrupar Colores similares
        $types = Type::all();
        $price = DB::table('products')->max('price');
        $price = round($price + $price * ($utilidad / 100), 2);
        $stock = DB::table('products')->max('stock');

        $nombre = '%' . $this->nombre . '%';
        $sku = '%' . $this->sku . '%';
        $color = $this->color;
        $category = $this->category;
        $proveedor = $this->proveedor == "" ? null : $this->proveedor;
        $type =  $this->type == "" ? null : $this->type;
        $precioMax = $price;
        if ($this->precioMax != null) {
            $precioMax =  round($this->precioMax / (($utilidad / 100) + 1), 2);
        }
        $precioMin = 0;
        if ($this->precioMin != null) {
            $precioMin =  round($this->precioMin / (($utilidad / 100) + 1), 2);
        }
        $stockMax =  $this->stockMax;
        $stockMin =  $this->stockMin;
        if ($stockMax == null) {
            $stockMax = $stock;
        }
        if ($stockMin == null) {
            $stockMin = 0;
        }

        $orderPrice = $this->orderPrice;
        $orderStock = $this->orderStock;

        $products  = Product::leftjoin('product_category', 'product_category.product_id', 'products.id')
            ->leftjoin('categories', 'product_category.category_id', 'categories.id')
            ->leftjoin('colors', 'products.color_id', 'colors.id')
            ->where('products.name', 'LIKE', $nombre)
            ->where('products.sku', 'LIKE', $sku)
            ->whereBetween('products.price', [$precioMin, $precioMax])
            ->whereBetween('products.stock', [$stockMin, $stockMax])
            ->where('products.provider_id', 'LIKE', $proveedor)
            ->where('products.type_id', 'LIKE', $type)
            ->when($orderStock !== '', function ($query, $orderStock) {
                $query->orderBy('products.stock', $this->orderStock);
            })
            ->when($orderPrice !== '', function ($query, $orderPrice) {
                $query->orderBy('products.price', $this->orderPrice);
            })
            ->when($color !== '' && $color !== null, function ($query, $color) {
                $newColor  = $this->color . '%';
                $query->where('colors.color', 'LIKE', $newColor);
            })
            ->when($category !== '' && $category !== null, function ($query, $category) {
                $newCat  = '%' . $this->category . '%';
                $query->where('categories.family', 'LIKE', $newCat);
            })
            ->orderBy('products.id', 'ASC')
            ->select('products.*')
            ->paginate(32);

        return view('cotizador.catalogo.view', [
            'products' => $products,
            'utilidad' => $utilidad,
            'proveedores' => $proveedores,
            'types' => $types,
            'price' => $price,
            'priceMax' => $precioMax,
            'priceMin' => $precioMin,
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
}
