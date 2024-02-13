<div>
    <style>
        .lds-dual-ring {
            display: inline-block;
            width: 30px;
            height: 30px;
        }

        .lds-dual-ring:after {
            content: " ";
            display: block;
            width: 24px;
            height: 24px;
            margin: 8px;
            border-radius: 50%;
            border: 6px solid #1FADD3;
            border-color: #1FADD3 transparent #1FADD3 transparent;
            animation: lds-dual-ring 1.2s linear infinite;
        }

        @keyframes lds-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .form-control {
            margin-bottom: 9px !important;
        }
    </style>
    <div class="d-flex h-100 w-100 justify-content-center align-items-center">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <h3 class="m-0">Nueva Importacion de Productos</h3>
                @if (!$archivo)
                    <p>Seleccione un archivo compatible (.csv, .xlsx) y siga los pasos para la importacion</p>
                @elseif (count($columns) > 0)
                    <p class="">Coloque el numero de columna en cada campo de los productos</p>
                @endif
            </div>
            <div class="w-100"></div>
            @if (!$archivo)
                <div class="col-md-10">
                    <form wire:submit.prevent="save">
                        <div class="">
                            <div class="form-group">
                                <div class="d-flex justify-content-between">
                                    <label for="">Selecionar Archivo</label>
                                    <div wire:loading>
                                        <div class="lds-dual-ring"></div>
                                    </div>
                                </div>
                                <input type="file"
                                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                    class="form-control" wire:model="fileLayout">
                                @error('fileLayout')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <br>
                            <div class="form-group text-center mt-3">
                                <button type="submit" class="btn btn-success">Ir al paso 2</button>
                            </div>
                        </div>

                    </form>
                    @error('fileLayout.*')
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif (count($columns) > 0 && count($productsImporteds) == 0)
                <div class="row w-100">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <label for="">Crear o actualizar</label>
                            <div class="form-group text-center m-0">
                                <button class="btn btn-sm btn-success" wire:click="firstStep()">Regresar al paso
                                    1</button>
                            </div>
                        </div>
                        <select wire:model="tipo" class="form-control   @error('tipo') border border-danger @enderror"
                            wire:change='typeChange'>
                            <option value="">Seleccione...</option>
                            <option value="create">Crear Productos</option>
                            <option value="update">Actualizar Productos</option>
                        </select>
                    </div>
                </div>
                <div class="row w-100">
                    <div class="col-md-8">
                        <form wire:submit.prevent="saveProductos()">

                            <h6>Informacion del producto</h6>
                            <div class="row">
                                @if ($updateProducts)
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="w-25">
                                                <label for="" style="display: block" class="m-0  d-block">
                                                    SKU Interno <span class="text-danger ">*</span>
                                                </label>
                                            </div>
                                            <div class="w-75">
                                                <input type="number" wire:model="SKU_interno"
                                                    placeholder="SKU generado por el sistema"
                                                    class="form-control p-0 m-0 text-center @error('SKU_interno') border border-danger @enderror">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                SKU Padre Proveedor
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="SKU_Padre"
                                                placeholder="SKU Padre del Proveedor (Opcional)"
                                                class="form-control p-0 m-0 text-center @error('SKU_Padre') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                SKU Proveedor
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="SKU"
                                                placeholder=" SKU del Proveedor (Opcional)"
                                                class="form-control p-0 m-0 text-center @error('SKU') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Nombre
                                                @if (!$updateProducts)
                                                    <span class="text-danger ">*</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Nombre" placeholder="Nombre del Producto"
                                                class="form-control p-0 m-0 text-center @error('Nombre') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Descripcion
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Descripcion"
                                                placeholder="Por defecto: Sin Descripcion"
                                                class="form-control p-0 m-0 text-center @error('Descripcion') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Precio Unico
                                                @if (!$updateProducts)
                                                    <span class="text-danger ">*</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Precio_Unico"
                                                placeholder="Colocar en el archivo 0 o 1"
                                                class="form-control p-0 m-0 text-center @error('Precio_Unico') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Precio
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Precio"
                                                placeholder="Precio por defecto: 0"
                                                class="form-control p-0 m-0 text-center @error('Precio') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Escala de Precios
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Escalas"
                                                placeholder="Colocar en el archivo las diferentes escalas"
                                                class="form-control p-0 m-0 text-center @error('Precio') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Cantidad
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Stock"
                                                placeholder="Cantidad por defecto: 0"
                                                class="form-control p-0 m-0 text-center @error('Stock') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Promocion
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Promocion"
                                                placeholder="Colocar en el archivo 0 o 1"
                                                class="form-control p-0 m-0 text-center @error('Promocion') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Descuento
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Descuento"
                                                placeholder="Porcentaje de descuento del Producto"
                                                class="form-control p-0 m-0 text-center @error('Descuento') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Producto Nuevo
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Nuevo_Producto"
                                                placeholder="Colocar 0 o 1, segun sea el caso"
                                                class="form-control p-0 m-0 text-center @error('Nuevo_Producto') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Tipo de Producto
                                                @if (!$updateProducts)
                                                    <span class="text-danger ">*</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Tipo"
                                                placeholder="Instrucciones de llenado en la guia"
                                                class="form-control p-0 m-0 text-center @error('Tipo') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Color
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Color"
                                                placeholder="Por defecto: Sin Color"
                                                class="form-control p-0 m-0 text-center @error('Color') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Proveedor
                                                @if (!$updateProducts)
                                                    <span class="text-danger ">*</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Proveedor"
                                                placeholder="Identificador del Proveedor."
                                                class="form-control p-0 m-0 text-center @error('Proveedor') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Familia
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Familia"
                                                placeholder="Por defecto: Vacio."
                                                class="form-control p-0 m-0 text-center @error('Familia') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                SubFamilia
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="SubFamilia"
                                                placeholder="Por defecto: Vacio."
                                                class="form-control p-0 m-0 text-center @error('SubFamilia') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Imagenes
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Imagenes"
                                                placeholder="Por defecto: Sin Imagen."
                                                class="form-control p-0 m-0 text-center @error('Imagenes') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="w-25">
                                            <label for="" style="display: block" class="m-0  d-block">
                                                Atributos
                                            </label>
                                        </div>
                                        <div class="w-75">
                                            <input type="number" wire:model="Atributos"
                                                placeholder="Intrucciones de llenado en la guia."
                                                class="form-control p-0 m-0 text-center @error('Atributos') border border-danger @enderror">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-center mt-3">
                                <button type="submit" class="btn btn-success">Comenzar Importacion</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <h6>Filas del archivo</h6>
                        <p style="color:red" >{{ $numeroMayorDeFila }}</p>
                        <h6>Columnas del Archivo</h6>
                        <ul class="list-group ">
                            <div class="row">

                                @foreach ($columns as $column)
                                    <div class="col-md-6">
                                        <li class="list-group-item py-1">
                                            {{ $column[0] . '. ' . $column[1] }}
                                        </li>
                                    </div>
                                @endforeach
                            </div>
                        </ul>
                    </div>
                </div>
            @elseif (count($productsImporteds) > 0)
                <div class="col-md-10">
                    Filas que no se importaron
                    <table class="table">
                        <thead>
                            <tr>
                                <td></td>
                                <td>Fila</td>
                                <td>Error</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filasError as $i)
                                <tr>
                                    <td></td>
                                    <td>{{ $i[0] }}</td>
                                    <td>{{ $i[1] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        </div>
    </div>
</div>
