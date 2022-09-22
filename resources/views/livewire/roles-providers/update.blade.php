<!-- Modal -->
<div wire:ignore.self class="modal fade" id="updateModal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Provider</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span wire:click.prevent="cancel()" aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($updateMode)
                    <ul class="list-group">
                        @foreach ($providers as $provider)
                            @php
                                $check = false;
                            @endphp
                            @foreach ($roleEdit->providers as $item)
                                @php
                                    if ($item->id == $provider->id) {
                                        $check = true;
                                    }
                                @endphp
                            @endforeach
                            <li class="list-group-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $provider->id }}"
                                        id="check{{ $provider->id }}" wire:click="updateProvider({{ $provider->id }})"
                                        {{ $check ? 'checked' : '' }}>
                                    <label class="form-check-label" for="check{{ $provider->id }}">
                                        {{ $provider->company }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" wire:click.prevent="cancel()" class="btn btn-secondary"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
