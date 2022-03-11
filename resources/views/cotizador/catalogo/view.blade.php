@section('title', __('Products'))
<div class="container-fluid ">
    <div class="row">
        <div class="col-md-2">
            @if (session()->has('message'))
                <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                    {{ session('message') }} </div>
            @endif
            <p>Filtros de busqueda</p>
            <input wire:model='keyWord' type="text" class="form-control" name="search" id="search"
                placeholder="Buscar por cualquier valor">
        </div>
        <div class="col-md-10">
            <div class="d-flex flex-wrap justify-content-between">
                @php
                    $counter = $products->perPage() * $products->currentPage() - $products->perPage() + 1;
                    $utilidad = (float) $utilidad->value;
                @endphp
                @foreach ($products as $row)
                    <div class="card mb-4" style="width: 14rem;">
                        <img src="{{ $row->image }}" class="card-img-top" alt="{{ $row->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $row->name }}</h5>
                            <p class="card-text m-0 pt-1">{{ Str::limit($row->description, 50) }}</p>
                            <div class="d-flex justify-content-between">
                                <p class=" m-0 pt-1">Disponoble: {{ $row->stock }}</p>
                                <p class=" m-0 pt-1">$
                                    {{ round($row->price + $row->price * ($utilidad / 100), 2) }}</p>
                            </div>
                            {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</div>
