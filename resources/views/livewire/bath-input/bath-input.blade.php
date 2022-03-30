<div>
    <div wire:loading wire:target="fileLayout">
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
            <div wire:loading="save">
                <div class="w-100">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100">25%</div>
                    </div>
                </div>
                <div class="p-5 d-flex justify-content-center w-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            @if (!$archivo)
                <div class="col-md-7 ">
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
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Enviar Archivo</button>
                            </div>
                        </div>

                    </form>
                    @error('fileLayout.*')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="w-100 text-center">

                </div>
            @endif
        </div>
    </div>
</div>
