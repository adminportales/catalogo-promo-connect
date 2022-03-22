@section('title', __('Products'))
<div class="container-fluid">
    <div class="row justify-content-center {{ $showList }}">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <h4><i class="fab fa-laravel text-info"></i>
                                Productos </h4>
                        </div>
                        <div wire:poll.60s>
                            <code>
                                <h5>{{ now()->format('H:i:s') }} UTC</h5>
                            </code>
                        </div>
                        @if (session()->has('message'))
                            <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                                {{ session('message') }} </div>
                        @endif
                        <div>
                            <input wire:model='keyWord' type="text" class="form-control" name="search" id="search"
                                placeholder="Search Products">
                        </div>
                        <div class="btn btn-sm btn-info" data-toggle="modal" data-target="#createDataModal">
                            <i class="fa fa-plus"></i> Agregar
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead">
                                <tr>
                                    <td>#</td>
                                    <th>Internal Sku</th>
                                    <th>Sku</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Stock</th>
                                    <th>Imagen</th>
                                    <th>Color</th>
                                    <th>Proveedor</th>
                                    <td>ACTIONS</td>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = $products->perPage() * $products->currentPage() - $products->perPage() + 1;
                                    $utilidad = (float) $utilidad->value;
                                @endphp
                                @foreach ($products as $row)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $row->internal_sku }}</td>
                                        {{-- <td>{{ $row->sku_parent }}</td> --}}
                                        <td>{{ $row->sku }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>$ {{ round($row->price + $row->price * ($utilidad / 100), 2) }}</td>
                                        <td>{{ Str::limit($row->description, 50) }}</td>
                                        <td>{{ $row->stock }}</td>
                                        <td><img src="{{ $row->images[0]->image_url }}" class="img-fluid"
                                                alt="Sin imagen" style="max-width: 60px" srcset=""></td>
                                        <td>{{ $row->color->color }}</td>
                                        <td>{{ $row->provider->company }}</td>
                                        <td width="90">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item"
                                                        wire:click="showProduct({{ $row->id }})"><i
                                                            class="fa fa-edit"></i> Ver Detalles </a>
                                                    <a class="dropdown-item"
                                                        onclick="confirm('Confirm Delete Product id {{ $row->id }}? \nDeleted Products cannot be recovered!')||event.stopImmediatePropagation()"
                                                        wire:click="destroy({{ $row->id }})"><i
                                                            class="fa fa-trash"></i> Delete </a>
                                                </div>
                                            </div>
                                        </td>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="{{ $showProduct }}">
        @livewire('show-and-edit-product')
    </div>
</div>
