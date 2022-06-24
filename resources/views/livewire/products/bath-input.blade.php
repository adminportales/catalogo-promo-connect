<div>
    <div class="d-flex h-100 w-100 justify-content-center align-items-center">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <h3>Nueva Importacion de Productos</h3>
                @if (!$archivo)
                    <p>Seleccione un archivo compatible (.csv, .xlsx) y siga los pasos para la importacion</p>
                @elseif (count($columns) > 0)
                    <p>Coloque el numero de columna en cada campo de los productos</p>
                @endif
            </div>
            <div class="w-100"></div>
            @if (!$archivo)
                <div class="col-md-10">
                    <form wire:submit.prevent="save">
                        <div class="">
                            <div class="form-group">
                                <label for="">Selecionar Archivo</label>
                                <input type="file" class="form-control" wire:model="fileLayout">
                            </div>
                            <div class="form-group">
                                <label for="">Crear o actualizar</label>
                                <select wire:model="tipo" class="form-control">
                                    <option value="">Seleccione...</option>
                                    <option value="create">Crear Productos</option>
                                    <option value="update">Actualizar Productos</option>
                                </select>
                            </div>
                            <br>
                            <div class="form-group text-center mt-3">
                                <button type="submit" class="btn btn-success">Ir al paso 2</button>
                            </div>
                        </div>

                    </form>
                    @error('fileLayout.*')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="w-100 text-center">
                </div>
            @elseif (count($columns) > 0 && count($productsImporteds) == 0)
                @if ($tipo == 'create')
                    <form wire:submit.prevent="createProductos()" class="w-100">
                    @elseif($tipo == 'update')
                        <form wire:submit.prevent="updateProductos()" class="w-100">
                @endif
                <div class="row justify-content-center">
                    <div class="col-md-5">
                        <h4>Informacion del producto</h4>
                        <ul class="list-group">
                            @if ($updateProducts)
                                <li class="list-group-item py-1">
                                    <div class="d-flex align-items-center">
                                        <div class="w-50">
                                            <label for="">SKU Interno</label>
                                        </div>
                                        <div class="w-50">
                                            <input type="text" wire:model="SKU_interno" class="form-control p-0 m-0">
                                        </div>
                                    </div>
                                    @error('SKU_interno')
                                        <span class="error">
                                            <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                            </p>
                                        </span>
                                    @enderror
                                </li>
                            @endif
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">SKU</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="SKU" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('SKU')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}</p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">SKU Padre</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="SKU_Padre" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('SKU_Padre')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}</p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Nombre</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Nombre" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Nombre')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}</p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Descripcion</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Descripcion" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Descripcion')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}</p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Precio</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Precio" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Precio')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}</p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Stock</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Stock" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Stock')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}</p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Promocion</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Promocion" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Promocion')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}</p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Descuento</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Descuento" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Descuento')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}</p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Producto Nuevo</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Nuevo_Producto"
                                            class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Nuevo_Producto')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Precio Unico</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Precio_Unico" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Precio_Unico')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Tipo</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Tipo" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Tipo')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Color</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Color" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Color')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Proveedor</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Proveedor" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Proveedor')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Familia</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Familia" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Familia')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Sub Familia</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="SubFamilia" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('SubFamilia')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Imagenes</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Imagenes" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Imagenes')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Escalas de Precios</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Escalas" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Escalas')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                            <li class="list-group-item py-1">
                                <div class="d-flex align-items-center">
                                    <div class="w-50">
                                        <label for="">Atributos</label>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" wire:model="Atributos" class="form-control p-0 m-0">
                                    </div>
                                </div>
                                @error('Atributos')
                                    <span class="error">
                                        <p class="text-center text-danger" style="font-size: 15px">{{ $message }}
                                        </p>
                                    </span>
                                @enderror
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-5">
                        <h4>Columnas del Archivo</h4>
                        <ul class="list-group">
                            @foreach ($columns as $column)
                                <li class="list-group-item py-1">
                                    {{ $column[0] . '. ' . $column[1] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="form-group text-center mt-3">
                    <button class="btn btn-success" wire:click="firstStep()">Regresar al paso 1</button>
                </div>
                <div class="form-group text-center mt-3">
                    <button type="submit" class="btn btn-success">Comenzar Importacion</button>
                </div>
                </form>
            @elseif (count($productsImporteds) > 0)
                <div class="col-md-10">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productsImporteds as $productImported)
                                <tr>
                                    <td class="m-0">{{ $productImported->internal_sku }}</td>
                                    <td class="m-0">{{ $productImported->name }}</td>
                                    <td class="m-0">{{ $productImported->price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div wire:loading.flex>
                <div class="p-5 text-success d-flex justify-content-center w-100">
                    <span>Espere...</span>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <table class="table">
    <thead>
        <tr>
            <th>SKU</th>
            <th>Nombre</th>
            <th>Precio</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($productsImporteds as $productImported)
            <tr>
                <td class="m-0">{{ $productImported->internal_sku }}</td>
                <td class="m-0">{{ $productImported->name }}</td>
                <td class="m-0">{{ $productImported->price }}</td>
            </tr>
        @endforeach
    </tbody>
</table> --}}
