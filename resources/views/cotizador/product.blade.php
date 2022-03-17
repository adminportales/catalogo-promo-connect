<div>
    <div wire:ignore.self class="modal fade" id="modalProduct" tabindex="-1" aria-labelledby="modalProductLabel"
        aria-hidden="true">
        <div class="modal-dialog">
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
                    <div class="modal-body px-5">
                        @php
                            $price = null;
                            if ($product->dinamycPrices->where('site_id', null)->first()) {
                                $price = round($product->price - $product->price * ($product->dinamycPrices->where('site_id', null)->first()->amount / 100), 2);
                            } else {
                                $price = $product->price;
                            }
                        @endphp
                        <div class="d-flex align-items-center flex-column">
                            <img src="{{ $product->images[0]->image_url }}" class=""
                                style="width: 200px; height: auto;" alt="{{ $product->name }}">
                            <div class="d-flex justify-content-around w-100 align-items-center">
                                <div class="">
                                    <p><strong>SKU: </strong> {{ $product->sku }}</p>
                                    <p><strong>SKU Padre: </strong> {{ $product->sku_parent }}</p>
                                </div>
                                <p><strong>SKU Interno: </strong> {{ $product->internal_sku }}</p>
                            </div>
                            <p><strong>Descripcion: </strong> {{ $product->description }}</p>
                            <p><strong>Color: </strong> {{ $product->color->color }}</p>
                            <p><strong>Stock: </strong> {{ $product->stock }}</p>
                            <p><strong>Proveedor: </strong> {{ $product->provider->company }}</p>
                            <p><strong>Precio: </strong>
                                $ {{ round($price + $price * ($utilidad / 100), 2) }}</p>

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
