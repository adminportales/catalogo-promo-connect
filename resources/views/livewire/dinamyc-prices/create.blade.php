<!-- Modal -->
<div wire:ignore.self class="modal fade" id="createDataModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="createDataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createDataModalLabel">Create New Dinamyc Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
				<form>
            <div class="form-group">
                <label for="type"></label>
                <input wire:model="type" type="text" class="form-control" id="type" placeholder="Type">@error('type') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="provider_change"></label>
                <input wire:model="provider_change" type="text" class="form-control" id="provider_change" placeholder="Provider Change">@error('provider_change') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="type_change"></label>
                <input wire:model="type_change" type="text" class="form-control" id="type_change" placeholder="Type Change">@error('type_change') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="amount"></label>
                <input wire:model="amount" type="text" class="form-control" id="amount" placeholder="Amount">@error('amount') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="product_id"></label>
                <input wire:model="product_id" type="text" class="form-control" id="product_id" placeholder="Product Id">@error('product_id') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="site_id"></label>
                <input wire:model="site_id" type="text" class="form-control" id="site_id" placeholder="Site Id">@error('site_id') <span class="error text-danger">{{ $message }}</span> @enderror
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
