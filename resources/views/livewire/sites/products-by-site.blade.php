<div>
    <style>
        .carousel-control-next-icon,
        .carousel-control-prev-icon {
            background-color: rgb(110, 103, 103) !important;
            border-radius: 15px !important;
            padding: 2px
        }

    </style>
    <div wire:ignore.self class="modal fade" id="productsModal" tabindex="-1" aria-labelledby="productsModalLabel"
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
                <div class="modal-header">
                    <h5 class="modal-title" id="productsModalLabel">Lista de productos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        wire:click="clear()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-2 py-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>$ {{ $product->price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        wire:click="clear()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
