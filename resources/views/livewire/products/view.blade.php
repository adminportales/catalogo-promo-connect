@section('title', __('Products'))
<div class="container-fluid ">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <h4><i class="fab fa-laravel text-info"></i>
                                Listado de Productos </h4>
                        </div>
                        {{-- <div wire:poll.1s>
                            <code>
                                <h5>{{ now()->format('H:i:s') }} UTC</h5>
                            </code>
                        </div> --}}
                        @if (session()->has('message'))
                            <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                                {{ session('message') }} </div>
                        @endif
                        <div style="display: flex; justify-content: space-between;  align-items: center;">
                            <div class="mx-3">
                                <input wire:model='keyWord' type="text" class="form-control" name="search" id="search"
                                    placeholder="Buscar">
                            </div>
                            <div>
                                <div class="btn btn-sm btn-info" data-toggle="modal" data-target="#createDataModal">
                                    <i class="fa fa-plus"></i> Agregar
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card-body">
                    @include('livewire.products.create')
                    @include('livewire.products.update')
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm ">
                            <thead class="thead">
                                <tr>
                                    <td>#</td>
                                    <th>Sku</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Descripcion</th>
                                    <th>Stock</th>
                                    <th>Tipo</th>
                                    <th>Color</th>
                                    <th>Imagen</th>
                                    {{-- <th>Offer</th>
                                    <th>Discount</th> --}}
                                    <th>Proveedor</th>
                                    <td>Acciones</td>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = $products->perPage() * $products->currentPage() - $products->perPage() + 1;
                                @endphp
                                @foreach ($products as $row)
                                    <tr>
                                        <td>{{ $counter++ }}
                                        </td>
                                        <td>{{ $row->sku }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>$ {{ $row->price }}</td>
                                        <td>{{ Str::limit($row->description, 50) }}</td>
                                        <td>{{ $row->stock }}</td>
                                        <td>{{ $row->type }}</td>
                                        <td>{{ $row->color }}</td>
                                        <td><img src="{{ $row->image }}" class="img-fluid" alt="Sin imagen"
                                                style="max-width: 60px" srcset=""></td>
                                        {{-- <td>{{ $row->offer }}</td>
                                        <td>{{ $row->discount }}</td> --}}
                                        <td>{{ $row->provider->company }}</td>
                                        <td width="90">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a data-toggle="modal" data-target="#updateModal"
                                                        class="dropdown-item"
                                                        wire:click="edit({{ $row->id }})"><i
                                                            class="fa fa-edit"></i> Editar </a>
                                                    <a class="dropdown-item"
                                                        onclick="confirm('Confirm Delete Product id {{ $row->id }}? \nDeleted Products cannot be recovered!')||event.stopImmediatePropagation()"
                                                        wire:click="destroy({{ $row->id }})"><i
                                                            class="fa fa-trash"></i> Eliminar </a>
                                                </div>
                                            </div>
                                        </td>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
