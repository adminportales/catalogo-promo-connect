<div class="container-fluid">
    @include('livewire.roles-providers.update')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <h4><i class="fab fa-laravel text-info"></i>
                                Visibilidad de Proveedores por Rol </h4>
                        </div>
                        @if (session()->has('message'))
                            <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                                {{ session('message') }} </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead">
                                <tr>
                                    <td>#</td>
                                    <th>Rol</th>
                                    <th>Proveedores Visibles</th>
                                    <td>Accion</td>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = $roles->perPage() * $roles->currentPage() - $roles->perPage() + 1;
                                @endphp
                                @foreach ($roles as $row)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $row->display_name }}</td>
                                        <td>
                                            @foreach ($row->providers as $provider)
                                                {{ $provider->company }},
                                            @endforeach
                                        </td>
                                        {{-- <td>{{ Str::limit($row->description, 50) }}</td> --}}

                                        <td width="90">
                                            <a data-toggle="modal" data-target="#updateModal" class="dropdown-item"
                                                wire:click="edit({{ $row->id }})"><i class="fa fa-edit"></i> Editar
                                            </a>
                                        </td>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
