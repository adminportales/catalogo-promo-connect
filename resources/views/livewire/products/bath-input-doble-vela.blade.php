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
                <h3 class="m-0">Nueva Importacion de Imagenes de Doble Vela</h3>
                @if (!$archivo)
                    <p>Seleccione un archivo compatible (.csv, .xlsx) y siga los pasos para la importacion</p>
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
            @endif
            @if ($importacionCorrecta)
                <h4>Importacion Correcta</h4>
            @endif
        </div>
    </div>
</div>
