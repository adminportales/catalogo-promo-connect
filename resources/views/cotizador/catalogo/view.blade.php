@section('title', __('Products'))
<div class="container-fluid ">
    <div class="row mb-2 d-md-none">
        <input type="text" class="form-control" placeholder="Buscar Productos">
    </div>
    <div class="row pb-2 d-sm-none">
        <div class="btn-group  w-100">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                + Filtros
            </button>
            <div class="dropdown-menu dropdown-menu-right w-100 shadow" wire:self>
                <div class="p-3">
                    <p>Filtros de busqueda</p>
                    <input wire:model='nombre' type="text" class="form-control mb-2" name="search" id="search"
                        placeholder="Nombre">
                    @permission('ver-sku-s-de-proveedores')
                        <input wire:model='sku' type="text" class="form-control mb-2" name="search" id="search"
                            placeholder="SKU">
                    @endpermission
                    <input wire:model='color' type="text" class="form-control mb-2" name="color" id="color"
                        placeholder="Ingrese el color">
                    <input wire:model='category' type="text" class="form-control mb-2" name="category" id="category"
                        placeholder="Ingrese la familia">
                    @permission('ver-proveedores')
                        <select wire:model='proveedor' name="proveedores" id="provee" class="form-control mb-2">
                            <option value="">Seleccione Proveedor...</option>
                            @foreach ($proveedores as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->company }}</option>
                            @endforeach
                        </select>
                    @endpermission
                    {{-- <select wire:model='color' name="colores" id="provee" class="form-control mb-2">
                <option value="">Seleccione color...</option>
                @foreach ($colores as $color)
                    <option value="{{ $color->id }}">{{ $color->color }}</option>
                @endforeach
                </select> --}}
                    <select wire:model='type' name="types" id="type" class="form-control mb-2">
                        <option value="">Importacion o Catalogo...</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->type }}</option>
                        @endforeach
                    </select>
                    @permission('ver-precio')
                        <p class="mb-0">Precio</p>
                        <div class="d-flex align-items-center mb-2">
                            <input wire:model='precioMin' type="number" class="form-control" name="search" id="search"
                                placeholder="Precio Minimo" min="0" value="0">
                            -
                            <input wire:model='precioMax' type="number" class="form-control" name="search" id="search"
                                placeholder="Precio Maximo" value="{{ $price }}" max="{{ $price }}">
                        </div>
                    @endpermission
                    @permission('ver-stock')
                        <p class="mb-0">Stock</p>
                        <div class="d-flex align-items-center mb-2">
                            <input wire:model='stockMin' type="number" class="form-control" placeholder="Stock Minimo"
                                min="0" value="0">
                            -
                            <input wire:model='stockMax' type="number" class="form-control" placeholder="Stock Maximo"
                                value="{{ $stock }}" max="{{ $stock }}">
                        </div>
                        <p class="mb-0">Ordenar por Stock</p>
                        <select wire:model='orderStock' name="orderStock" id="provee" class="form-control mb-2">
                            <option value="">Ninguno</option>
                            <option value="ASC">De menor a mayor</option>
                            <option value="DESC">De mayor a menor</option>
                        </select>
                    @endpermission
                    @permission('ver-precio')
                        <p class="mb-0">Ordenar por Precio</p>
                        <select wire:model='orderPrice' name="orderPrice" id="provee" class="form-control mb-2">
                            <option value="">Ninguno</option>
                            <option value="ASC">De menor a mayor</option>
                            <option value="DESC">De mayor a menor</option>
                        </select>
                    @endpermission
                    <button class="btn btn-primary btn-block" wire:click="limpiar">Limpiar Filtros</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2 col-md-4 col-sm-5  d-none d-sm-block">
            <div class="shadow p-3">
                <p>Filtros de busqueda</p>
                <input wire:model='nombre' type="text" class="form-control mb-2" name="search" id="search"
                    placeholder="Nombre">
                @permission('ver-sku-s-de-proveedores')
                    <input wire:model='sku' type="text" class="form-control mb-2" name="search" id="search"
                        placeholder="SKU">
                @endpermission
                <input wire:model='color' type="text" class="form-control mb-2" name="color" id="color"
                    placeholder="Ingrese el color">
                <input wire:model='category' type="text" class="form-control mb-2" name="category"
                    id="category" placeholder="Ingrese la familia">
                @permission('ver-proveedores')
                    <select wire:model='proveedor' name="proveedores" id="provee" class="form-control mb-2">
                        <option value="">Seleccione Proveedor...</option>
                        @foreach ($proveedores as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->company }}</option>
                        @endforeach
                    </select>
                @endpermission
                {{-- <select wire:model='color' name="colores" id="provee" class="form-control mb-2">
                <option value="">Seleccione color...</option>
                @foreach ($colores as $color)
                    <option value="{{ $color->id }}">{{ $color->color }}</option>
                @endforeach
                </select> --}}
                <select wire:model='type' name="types" id="type" class="form-control mb-2">
                    <option value="">Importacion o Catalogo...</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}">{{ $type->type }}</option>
                    @endforeach
                </select>

                @permission('ver-precio')
                    <p class="mb-0">Precio</p>
                    <div class="d-flex align-items-center mb-2">
                        <input wire:model='precioMin' type="number" class="form-control" name="search" id="search"
                            placeholder="Precio Minimo" min="0" value="0">
                        -
                        <input wire:model='precioMax' type="number" class="form-control" name="search" id="search"
                            placeholder="Precio Maximo" value="{{ $price }}" max="{{ $price }}">
                    </div>
                @endpermission
                @permission('ver-stock')
                    <p class="mb-0">Stock</p>
                    <div class="d-flex align-items-center mb-2">
                        <input wire:model='stockMin' type="number" class="form-control" placeholder="Stock Minimo"
                            min="0" value="0">
                        -
                        <input wire:model='stockMax' type="number" class="form-control" placeholder="Stock Maximo"
                            value="{{ $stock }}" max="{{ $stock }}">
                    </div>
                    <p class="mb-0">Ordenar por Stock</p>
                    <select wire:model='orderStock' name="orderStock" id="provee" class="form-control mb-2">
                        <option value="">Ninguno</option>
                        <option value="ASC">De menor a mayor</option>
                        <option value="DESC">De mayor a menor</option>
                    </select>
                @endpermission
                @permission('ver-precio')
                    <p class="mb-0">Ordenar por Precio</p>
                    <select wire:model='orderPrice' name="orderPrice" id="provee" class="form-control mb-2">
                        <option value="">Ninguno</option>
                        <option value="ASC">De menor a mayor</option>
                        <option value="DESC">De mayor a menor</option>
                    </select>
                @endpermission
                <button class="btn btn-primary btn-block" wire:click="limpiar">Limpiar Filtros</button>
            </div>
        </div>
        <div class="products col-lg-10 col-md-8 col-sm-7">
            @php
                $counter = $products->perPage() * $products->currentPage() - $products->perPage() + 1;
            @endphp
            <div wire:loading.block>
                <div class="loading w-100 d-flex justify-content-center align-items-center" style="height: 85vh;">
                    <div class="w-25">
                        <img src="{{ asset('img/load1.gif') }}" alt="" srcset="" class="w-100">
                    </div>
                </div>
            </div>
            <div class="row" wire:loading.class='products-content'>
                @if (count($products) <= 0)
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap justify-content-center align-items-center flex-column">
                            <p>No hay resultados de busqueda en la pagina actual</p>
                            @if (count($products->items()) == 0 && $products->currentPage() > 1)
                                <p>Click en la paginacion para ver mas resultados</p>
                            @endif
                        </div>
                    </div>
                @endif
                @foreach ($products as $row)
                    {{-- {{ $row->firstImage->image_url }} --}}
                    <div class="col-md-4 col-lg-3 col-sm-6  d-none d-sm-flex justify-content-center">
                        <div class="card mb-4" style="width: 14rem;">
                            <div class="card-body text-center shadow-sm">
                                @php
                                    $priceProduct = $row->price;
                                    if ($row->producto_promocion) {
                                        $priceProduct = round(
                                            $priceProduct - $priceProduct * ($row->descuento / 100),
                                            2,
                                        );
                                    } else {
                                        $priceProduct = round(
                                            $priceProduct - $priceProduct * ($row->provider->discount / 100),
                                            2,
                                        );
                                    }
                                @endphp
                                <img src="{{ $row->firstImage ? $row->firstImage->image_url : '' }}"
                                    class="card-img-top " alt="{{ $row->name }}"
                                    style="max-width: 100%; max-height: 150px; width: auto">
                                <hr>
                                <h5 class="card-title" style="text-transform: capitalize">{{ $row->name }}</h5>
                                @permission('ver-sku-s-de-proveedores')
                                    <p class=" m-0 pt-1"><strong>SKU:</strong> {{ $row->sku }}</p>
                                @endpermission
                                <div class="d-flex justify-content-between">
                                    @permission('ver-stock')
                                        <p class=" m-0 pt-1">Stock: {{ $row->stock }}</p>
                                    @endpermission
                                    @permission('ver-precio')
                                        <p class=" m-0 pt-1">$
                                            {{ round($priceProduct / ((100 - $utilidad) / 100), 2) }}</p>
                                    @endpermission
                                </div>
                                <br>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#modalProduct" wire:click="showProduct({{ $row->id }})">
                                    Vista Rapida
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-block d-sm-none" style="width: 100%;">
                        <div class="card mb-2" style="width: 100%;" data-toggle="modal" data-target="#modalProduct"
                            wire:click="showProduct({{ $row->id }})">
                            <div class="card-body text-center shadow-sm d-flex">
                                @php
                                    $priceProduct = $row->price;
                                    if ($row->producto_promocion) {
                                        $priceProduct = round(
                                            $priceProduct - $priceProduct * ($row->descuento / 100),
                                            2,
                                        );
                                    } else {
                                        $priceProduct = round(
                                            $priceProduct - $priceProduct * ($row->provider->discount / 100),
                                            2,
                                        );
                                    }
                                @endphp
                                <div style="width: 35%">
                                    <img src="{{ $row->firstImage ? $row->firstImage->image_url : '' }}"
                                        alt="{{ $row->name }}"
                                        style="max-width: 100px; max-height: 100px; height: 100px; width: auto">
                                </div>
                                <div class="text-left">
                                    <h5 class="card-title p-0" style="text-transform: capitalize">
                                        {{ $row->name }}
                                    </h5>
                                    <p class=" m-0 pt-0">{{ Str::limit($row->description, 25, '...') }}</p>
                                    @permission('ver-sku-s-de-proveedores')
                                        <p class="m-0 pt-0"><strong>SKU:</strong> {{ $row->sku }}</p>
                                    @endpermission

                                    <div class="d-flex justify-content-between">
                                        @permission('ver-stock')
                                            <p class=" m-0 pt-0">Stock: {{ $row->stock }}</p>
                                        @endpermission
                                        @permission('ver-precio')
                                            <p class=" m-0 pt-0">Precio: $
                                                {{ round($priceProduct / ((100 - $utilidad) / 100), 2) }}</p>
                                        @endpermission
                                    </div>
                                </div>
                                {{-- <div class="al">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#modalProduct"
                                            wire:click="showProduct({{ $row->id }})">
                                            Ver
                                        </button>
                                    </div> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex d-sm-none  justify-content-center">
                {{ $products->onEachSide(0)->links() }}
            </div>
            <div class="d-none d-sm-flex justify-content-center">
                {{ $products->onEachSide(3)->links() }}
            </div>
            @livewire('product')
        </div>
    </div>
    <style>
        .products {
            position: relative;
        }

        .products-content {
            opacity: .3;
        }

        .loading {
            z-index: 100;
            position: absolute;
            opacity: 1;
        }
    </style>
</div>
