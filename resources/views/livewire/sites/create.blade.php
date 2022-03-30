<!-- Modal -->
<div wire:ignore.self class="modal fade" id="createDataModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="createDataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createDataModalLabel">Create New Site</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
				<form>
            <div class="form-group">
                <label for="name"></label>
                <input wire:model="name" type="text" class="form-control" id="name" placeholder="Name">@error('name') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="utility"></label>
                <input wire:model="utility" type="text" class="form-control" id="utility" placeholder="Utility">@error('utility') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="woocommerce"></label>
                <input wire:model="woocommerce" type="text" class="form-control" id="woocommerce" placeholder="Woocommerce">@error('woocommerce') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="url"></label>
                <input wire:model="url" type="text" class="form-control" id="url" placeholder="Url">@error('url') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="consumer_key"></label>
                <input wire:model="consumer_key" type="text" class="form-control" id="consumer_key" placeholder="Consumer Key">@error('consumer_key') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="consumer_secret"></label>
                <input wire:model="consumer_secret" type="text" class="form-control" id="consumer_secret" placeholder="Consumer Secret">@error('consumer_secret') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
                <button type="button" wire:click.prevent="store()" class="btn btn-primary close-modal">Save</button>
            </div>
        </div>
    </div>
</div>
