<div>

    @if (!$product)
        <div wire:loading>
            <div class="p-5 d-flex justify-content-center w-100">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    @endif
    @if ($product)
        <div class="d-flex justify-content-between">
            <div>
                <h3>Editar Producto</h3>
            </div>
            <div>
                <button class="btn btn-warning" wire:click="showList()">Regresar</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input class="form-control" type="text" placeholder="Nombre del Producto"
                    value="{{ $product->name }}">
                <div class="py-2"></div>
                <textarea class="form-control" name="" id="" cols="30" rows="2">{{ $product->description }}</textarea>
                @error('description')
                    <span class="error text-danger">{{ $message }}</span>
                @enderror
                <br>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Sitios en donde se comparte</h5>
                    </div>
                    <div class="card-body">
                        <h5>Informacion del producto</h5>
                        <form>
                            <div class="form-group py-1">
                                <div class="d-flex align-items-center">
                                    <label for="internal_sku" class="w-25">Sku Int</label>
                                    <input type="text" class="form-control" id="internal_sku"
                                        placeholder="Internal Sku" value="{{ $product->internal_sku }}" disabled>
                                </div>
                                @error('internal_sku')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="sku_parent" class="w-25">Sku Padre</label>
                                    <input type="text" class="form-control" id="sku_parent" placeholder="Sku Parent"
                                        value="{{ $product->sku_parent }}">
                                </div>
                                @error('sku_parent')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="sku" class="w-25">Sku</label>
                                    <input type="text" class="form-control" id="sku" placeholder="Sku"
                                        value="{{ $product->sku }}">
                                </div>
                                @error('sku')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="price" class="w-25">Precio</label>
                                    <input type="number" class="form-control" id="price" placeholder="Price"
                                        value="{{ $product->price }}">
                                </div>
                                @error('price')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="stock" class="w-25">Stock</label>
                                    <input type="text" class="form-control" id="stock" placeholder="Stock"
                                        value="{{ $product->stock }}">
                                </div>
                                @error('stock')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="type" class="w-25">Tipo</label>
                                    <select class="form-control" name="type" id="">
                                        <option value="">Seleccione</option>
                                        @foreach ($types as $item)
                                            <option {{ $item->id == $product->type_id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('type_id')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="color" class="w-25">Color</label>
                                    <select class="form-control" name="" id="">
                                        <option value="">Seleccione</option>
                                        @foreach ($colors as $item)
                                            <option {{ $item->id == $product->color_id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->color }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('color_id')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="type" class="w-25">Proveedor</label>
                                    <select class="form-control" name="" id="">
                                        <option value="">Seleccione</option>
                                        @foreach ($providers as $item)
                                            <option {{ $item->id == $product->provider_id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->company }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('provider_id')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-4">
                <h5>Imagenes del Producto</h5>
            </div> --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Sitios en donde se comparte</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($sites as $site)
                                @php
                                    $check = false;
                                @endphp
                                @foreach ($product->sitesProducts as $item)
                                    @php
                                        if ($item->id == $site->id) {
                                            $check = true;
                                        }
                                    @endphp
                                @endforeach
                                <li class="list-group-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $site->id }}"
                                            id="check{{ $site->id }}"
                                            wire:click="updateSites({{ $site->id }})"
                                            {{ $check ? 'checked' : '' }}>
                                        <label class="form-check-label" for="check{{ $site->id }}">
                                            {{ $site->name }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        @if (session()->has('updateSites'))
                            <div wire:poll.3s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                                {{ session('updateSites') }} </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                @livewire('dinamyc-prices', ['product' => $product])
            </div>
        </div>
    @endif
</div>
