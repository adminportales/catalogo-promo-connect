<div>
    <style>
        .carousel-control-next-icon,
        .carousel-control-prev-icon {
            background-color: rgb(110, 103, 103) !important;
            border-radius: 15px !important;
            padding: 2px
        }

    </style>
    <div wire:ignore.self class="modal fade" id="modalProduct" tabindex="-1" aria-labelledby="modalProductLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div wire:loading>
                    <div class="p-5 d-flex justify-content-center w-100">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                @if ($product)
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalProductLabel">{{ $product->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="clear()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-2 py-0">
                        @if ($product->precio_unico)
                            @php
                                $priceProduct = $product->price;
                                $price = round($priceProduct - $priceProduct * ($product->provider->discount / 100), 2);
                            @endphp
                        @endif
                        <div class="d-flex align-items-center flex-column">
                            <div class="d-flex justify-content-center">
                                <div id="carouselExampleControls" class="carousel slide w-50" data-ride="carousel">
                                    <div class="carousel-inner">
                                        @php
                                            $active = 0;
                                        @endphp
                                        @if ($product->images)
                                            @foreach ($product->images as $image)
                                                @if ($image->image_url != null)
                                                    @if ($active == 0)
                                                        @php
                                                            $active = 1;
                                                        @endphp
                                                    @endif
                                                    <div class="carousel-item  {{ $active == 1 ? 'active' : '' }}">
                                                        <img src="{{ $image->image_url }}" class="d-block w-100"
                                                            alt="{{ $image->image_url }}">
                                                    </div>
                                                    @php
                                                        $active = 2;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                                        data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                                        data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>

                            </div>
                            <div class="d-flex justify-content-around w-100 align-items-center">
                                <div class="">
                                    <p><strong>SKU: </strong> {{ $product->sku }}</p>
                                    <p><strong>SKU Padre: </strong> {{ $product->sku_parent }}</p>
                                </div>
                                <p><strong>SKU Interno: </strong> {{ $product->internal_sku }}</p>
                            </div>
                            <div class="row w-100">
                                <div class="col-md-6">
                                    @php
                                        $priceProduct = $product->price;
                                        if ($product->producto_promocion) {
                                            $priceProduct = round($priceProduct - $priceProduct * ($product->descuento / 100), 2);
                                        } else {
                                            $priceProduct = round($priceProduct - $priceProduct * ($product->provider->discount / 100), 2);
                                        }
                                    @endphp
                                    <h5><strong>Informacion</strong></h5>
                                    <p><strong>Descripcion: </strong> {{ $product->description }}</p>
                                    <p><strong>Color: </strong> {{ $product->color->color }}</p>
                                    <p><strong>Stock: </strong> {{ $product->stock }}</p>
                                    <p><strong>Proveedor: </strong> {{ $product->provider->company }}</p>
                                    @if ($product->precio_unico)
                                        <p><strong>Precio: </strong>
                                            $ {{ round($priceProduct + $price * ($utilidad / 100), 2) }}</p>
                                    @endif
                                    <p><strong>Producto Nuevo: </strong> {{ $product->producto_nuevo ? 'SI' : 'NO' }}
                                    </p>
                                    <p><strong>Producto de Promocion: </strong>
                                        {{ $product->producto_promocion ? 'SI' : 'NO' }}</p>
                                    @if (!$product->precio_unico)
                                        <h5><strong>Precios</strong></h5>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Escala</th>
                                                    <th>Precio</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($product->precios as $precio)
                                                    @php
                                                        $priceProduct = $product->price;
                                                        $price = round($priceProduct - $priceProduct * ($product->provider->discount / 100), 2);
                                                        $precioFinal = round($price + $price * ($utilidad / 100), 2);
                                                    @endphp
                                                    <tr>
                                                        <td class="p-0">{{ $precio->escala }}</td>
                                                        <td class="p-0">$ {{ $precioFinal }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h5><strong>Categorias</strong></h5>
                                    <p><strong>Categoria:</strong>
                                        {{ $product->productCategories[0]->category->family }}
                                    </p>
                                    <p><strong>Sub
                                            categoria:</strong>
                                        {{ $product->productCategories[0]->subcategory->subfamily }}
                                    </p>
                                    @if (count($product->productAttributes) > 0)
                                        <h5><strong>Otros Atributos</strong></h5>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Atributo</th>
                                                    <th>Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($product->productAttributes as $attr)
                                                    <tr>
                                                        <td class="p-0">{{ $attr->attribute }}</td>
                                                        <td class="p-0">{{ $attr->value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            wire:click="clear()">Cerrar</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
