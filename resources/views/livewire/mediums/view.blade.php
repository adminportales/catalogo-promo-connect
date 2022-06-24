@section('title', __('Mediums'))
@section('styles')
    <style>
        .info {
            position: absolute;
            bottom: -100%;
            background-color: #f9ce2400;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease-in-out;
        }

        .imagen {
            position: relative;
            overflow: hidden;
        }

        .imagen:hover .info {
            bottom: 0%;
            display: flex;
            background-color: #bebaabad;
        }
    </style>
@endsection
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <h4><i class="fab fa-laravel text-info"></i>
                                Listado de Medios </h4>
                        </div>
                        @if (session()->has('message'))
                            <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                                {{ session('message') }} </div>
                        @endif
                        <div>
                            <input wire:model='keyWord' type="text" class="form-control" name="search"
                                id="search" placeholder="Search Mediums">
                        </div>
                        <div class="btn btn-sm btn-info" data-toggle="modal" data-target="#createDataModal">
                            <i class="fa fa-plus"></i> Add Mediums
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @livewire('upload-media')

                    <br>
                    <div class="row">
                        @foreach ($media as $row)
                            <div class="col-md-4 col-lg-3 col-xl-2 p-1 imagen" style="object-fit: cover">
                                <img src="{{ $row->path }}" alt="{{ $row->name }}" srcset=""
                                    style="max-width: 100%; max-height: 150px; height: 100%; object-fit: cover; width: 100%"
                                    class="shadow rounded">
                                <div class="info">
                                    <a class="btn btn-danger mx-1"
                                        onclick="confirm('Confirm Delete Medium id {{ $row->id }}? \nDeleted Mediums cannot be recovered!')||event.stopImmediatePropagation()"
                                        wire:click="destroy({{ $row->id }})"><i class="fa fa-trash"></i>
                                    </a>
                                    <button class="btn btn-info btn-copy mx-1" onclick="copy('{{ $row->path }}')"><i class="fa fa-copy"></i></button>
                                    <!-- The text field -->
                                    <input type="hidden" value="{{ $row->path }}" class="input-value">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{ $media->links() }}
                </div>
            </div>
        </div>
    </div>


    @section('scripts')
        <script>
            function copy(text1) {
                console.log(text1);
                Swal.fire('Copiar la url de la imagen: <br><br><p style="font-size:18px;">' + text1 + '</p>')
            }
        </script>
    @endsection
</div>
