@section('title', __('Dinamyc Prices'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="fab fa-laravel text-info"></i>
							Dinamyc Price Listing </h4>
						</div>
						<div wire:poll.60s>
							<code><h5>{{ now()->format('H:i:s') }} UTC</h5></code>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Dinamyc Prices">
						</div>
						<div class="btn btn-sm btn-info" data-toggle="modal" data-target="#createDataModal">
						<i class="fa fa-plus"></i>  Add Dinamyc Prices
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.dinamycPrices.create')
						@include('livewire.dinamycPrices.update')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Type</th>
								<th>Provider Change</th>
								<th>Type Change</th>
								<th>Amount</th>
								<th>Product Id</th>
								<th>Site Id</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@foreach($dinamycPrices as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->type }}</td>
								<td>{{ $row->provider_change }}</td>
								<td>{{ $row->type_change }}</td>
								<td>{{ $row->amount }}</td>
								<td>{{ $row->product_id }}</td>
								<td>{{ $row->site_id }}</td>
								<td width="90">
								<div class="btn-group">
									<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Actions
									</button>
									<div class="dropdown-menu dropdown-menu-right">
									<a data-toggle="modal" data-target="#updateModal" class="dropdown-item" wire:click="edit({{$row->id}})"><i class="fa fa-edit"></i> Edit </a>							 
									<a class="dropdown-item" onclick="confirm('Confirm Delete Dinamyc Price id {{$row->id}}? \nDeleted Dinamyc Prices cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"><i class="fa fa-trash"></i> Delete </a>   
									</div>
								</div>
								</td>
							@endforeach
						</tbody>
					</table>						
					{{ $dinamycPrices->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
