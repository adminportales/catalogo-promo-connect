<!-- Modal -->
<div wire:ignore.self class="modal fade" id="updateModal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span wire:click.prevent="cancel()" aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" wire:model="selected_id">
                    <div class="form-group">
                        <label for="internal_sku"></label>
                        <input wire:model="internal_sku" type="text" class="form-control" id="internal_sku"
                            placeholder="internal_sku">
                        @error('internal_sku')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="sku"></label>
                        <input wire:model="sku" type="text" class="form-control" id="sku" placeholder="Sku">
                        @error('sku')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="name"></label>
                        <input wire:model="name" type="text" class="form-control" id="name" placeholder="Name">
                        @error('name')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="price"></label>
                        <input wire:model="price" type="text" class="form-control" id="price" placeholder="Price">
                        @error('price')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description"></label>
                        <input wire:model="description" type="text" class="form-control" id="description"
                            placeholder="Description">
                        @error('description')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="stock"></label>
                        <input wire:model="stock" type="text" class="form-control" id="stock" placeholder="Stock">
                        @error('stock')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="type"></label>
                        <input wire:model="type" type="text" class="form-control" id="type" placeholder="Type">
                        @error('type')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="color"></label>
                        <input wire:model="color" type="text" class="form-control" id="color" placeholder="Color">
                        @error('color')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="image"></label>
                        <input wire:model="image" type="text" class="form-control" id="image" placeholder="Image">
                        @error('image')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="offer">Para ecommerce</label>
                        <input wire:model="ecommerce" type="checkbox" class="form-control" id="ecommerce"
                            placeholder="Para ecommerce">
                        @error('ecommerce')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="offer"></label>
                        <input wire:model="offer" type="text" class="form-control" id="offer" placeholder="Offer">
                        @error('offer')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="discount"></label>
                        <input wire:model="discount" type="text" class="form-control" id="discount"
                            placeholder="Discount">
                        @error('discount')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="provider_id"></label>
                        <input wire:model="provider_id" type="text" class="form-control" id="provider_id"
                            placeholder="Provider Id">
                        @error('provider_id')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click.prevent="cancel()" class="btn btn-secondary"
                    data-dismiss="modal">Close</button>
                <button type="button" wire:click.prevent="update()" class="btn btn-primary"
                    data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
