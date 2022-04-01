<div>
    <div class="d-flex h-100 w-100 justify-content-center align-items-center">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <h3>Nueva Importacion de Productos</h3>
                <p>Seleccione un archivo compatible (.csv, .xlsx) y siga los pasos para la importacion</p>
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
            @else
                <div class="col-md-10">
                    <div wire:loading.flex>
                        <div class="p-5 text-success d-flex justify-content-center w-100">
                            <span>Importando...</span>
                        </div>
                    </div>
                    <ul class="list-group">
                        @foreach ($productsImporteds as $productImported)
                            <li class="list-group-item">
                                <p class="m-0">{{ $productImported }}</p>
                            </li>
                        @endforeach
                    </ul>
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
