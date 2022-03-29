<div>
    <div wire:loading>
        <div class="p-5 d-flex justify-content-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div class="d-flex h-100 w-100 justify-content-center align-items-center">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <h3>Nueva Importacion</h3>
            </div>
            <div class="w-100">
                <br>
            </div>
            @if (!$archivo)
                <div class="col-md-5 ">
                    <form wire:submit.prevent="save">
                        <div class="">
                            <div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="inputGroupFile01"
                                            wire:model="fileLayout" aria-describedby="inputGroupFileAddon01">
                                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Enviar Archivo</button>
                            </div>
                        </div>

                    </form>
                    @error('fileLayout.*')
                        <span class="error">{{ $message }}</span>
                    @enderror
                    {{-- <input type="file"  class=" "> --}}
                    {{-- <button class="">Cargar Archivo</button> --}}
                </div>
                <div class="w-100 text-center">
                </div>
            @endif
            @if ($archivo)
                <div class="col-md-10 row">
                    <div class="col-md-12 text-center">
                        <p>{{ $archivo }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Informacion del producto</h5>
                        <form>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="sku_parent" class="w-25">Sku Padre</label>
                                    <input type="text" class="form-control" id="sku_parent" placeholder="Sku Parent"
                                        value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="sku" class="w-25">Sku</label>
                                    <input type="text" class="form-control" id="sku" placeholder="Sku" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="nombre" class="w-25">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" placeholder="Nombre" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="desc" class="w-25">Descripcion</label>
                                    <input type="text" class="form-control" id="desc" placeholder="Descripcion"
                                        value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="price" class="w-25">Precio</label>
                                    <input type="number" class="form-control" id="price" placeholder="Price" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="stock" class="w-25">Descuento</label>
                                    <input type="text" class="form-control" id="stock" placeholder="Stock" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="price" class="w-25">Color</label>
                                    <input type="number" class="form-control" id="price" placeholder="Price" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="stock" class="w-25">Stock</label>
                                    <input type="text" class="form-control" id="stock" placeholder="Stock" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="proveedor" class="w-25">Proveedor</label>
                                    <input type="text" class="form-control" id="proveedor" placeholder="Proveedor"
                                        value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="images" class="w-25">Imagenes</label>
                                    <input type="text" class="form-control" id="images" placeholder="Imagenes"
                                        value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="familia" class="w-25">Familia</label>
                                    <input type="text" class="form-control" id="familia" placeholder="Familia"
                                        value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center ">
                                    <label for="subfamilia" class="w-25">Sub Familia</label>
                                    <input type="text" class="form-control" id="subfamilia" placeholder="Sub Familia"
                                        value="">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <h5>Columnas del archivo</h5>
                        <ul>
                            @foreach ($columns as $column)
                                <li>{{ $column }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- jsDelivr :: Sortable :: Latest (https://www.jsdelivr.com/package/npm/sortablejs) -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
</div>
