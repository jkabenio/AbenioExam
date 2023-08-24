@extends('layouts.app')
@section('content')
<style>
    td{
        width:14.28%;
    }
    th{
        text-align: center;
    }
    .container-md{
        width: 100%;
    }
</style>
<div>
    <h1 align="center"> <b>Product List</b> </h1>
</div>
<div class="container-md">
    
    
    <div style="float: left; margin-bottom: 0.5rem">
        <a href="/product" class="btn btn-primary">Create Product</a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Product Name</th>
                <th scope="col">Unit</th>
                <th scope="col">Price</th>
                <th scope="col">Date of Expiry</th>
                <th scope="col">Available Inventory</th>
                <th scope="col">Image</th>
                <th scope="col">Update</th>
                <th scope="col">Delete</th>
            </tr>
        </thead>

       
        <tbody id="product-list">
            @foreach($sortedProducts as $index => $product)
            <tr>
                <th scope="row">{{ $index + 1 }}</th>
                <td>{{ $product->prod_name }}</td>
                <td>{{ $product->unit }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ date('F d, Y', strtotime($product->expiration_date)) }}</td>
                @php
                    $cost = $product->available * $product->price;
                @endphp
                <td>Quantity: {{ $product->available }}<br>Total Cost: {{ number_format($cost, 2) }}</td>
                <td>
                    @if($product->image)
                    <img src="{{ asset('upload_images/' . $product->image) }}" alt="product image" width="50%" height="50%">
                    @else
                    No image available
                    @endif
                </td>
                <td><!-- Add this button wherever you want it in your Blade view -->
                    <button class="btn btn-success update-product" data-product-id="{{ $product->id }}">Edit</button>

                </td>
                <td>
                    <button class="btn btn-danger delete-product" data-product-id="{{ $product->id }}">Delete</button>
                </td>                
            </tr>
            @endforeach
        </tbody>
    </table>
    
</div>


<!-- Include Bootstrap CSS and JavaScript in your layout if you haven't already -->

<!-- Modal for Editing Product -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <div style="float: right">
                    <button type="button" class="btn-close"  data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
            </div>
            <form id="editProductForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Add form fields for editing product properties here -->
                    <input type="hidden" id="editProductId" name="product_id">
                    <div class="form-group">
                        <label for="editProdName"><span style="color: red">*</span> Product Name</label>
                        <input type="text" class="form-control" id="editProdName" name="prod_name">

                        <label for="editProdUnit"><span style="color: red">*</span> Product Unit</label>
                        <input type="text" class="form-control" id="editProdUnit" name="unit">

                        <label for="editProdPrice"><span style="color: red">*</span> Product Price</label>
                        <input type="number" class="form-control" id="editProdPrice" step="any" name="price">

                        <label for="editProdExpiryDate"><span style="color: red">*</span> Date of Expiry</label>
                        <input type="date" class="form-control" id="editProdExpiryDate" name="expiration_date">

                        <label for="editProdAvailable"><span style="color: red">*</span> Product Inventory</label>
                        <input type="number" class="form-control" id="editProdAvailable" name="available">

                        <label id="editProdImage">New Product Image</label>
                        <input type="file" class="form-control" id="editProdImage" name="image">
                    </div>
                    <!-- Add other fields for editing here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    // Add JavaScript to display the selected file name
    $('#editProdImage').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $('#selectedFileName').text('Selected File: ' + fileName);
    });
</script>


<!-- Place this button wherever you want to trigger the AJAX update -->
<script>
    $(document).ready(function() {
    $('#update-products').click(function() {
        $.ajax({
            type: 'GET',
            url: '{{ route("show-product.show") }}',
            success: function(products) {
                // Handle the retrieved data here
                displayProducts(products);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    function loadProducts() {
        $.ajax({
            type: 'GET',
            url: '{{ route("show-product.show") }}',
            success: function(products) {
                // Handle the retrieved data here
                displayProducts(products);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Update button click event
    $('.update-product').click(function() {
        var productId = $(this).data('product-id');

        // Fetch the product details for editing (you can use an AJAX request here)
        $.ajax({
            type: 'GET',
            url: '{{ route("product.edit", ":id") }}'.replace(':id', productId),
            data: {
                product_id: productId
            },
            success: function(response) {
                // Populate the form fields with the existing data
                $('#editProductId').val(response.product.id);
                $('#editProdName').val(response.product.prod_name);
                $('#editProdUnit').val(response.product.unit);
                $('#editProdPrice').val(response.product.price);
                $('#editProdExpiryDate').val(response.product.expiration_date);
                $('#editProdAvailable').val(response.product.available);
                
                // Set the image preview (if an image exists)
                if (response.product.image) {
                    $('#selectedFileName').text('Selected File: ' + response.product.image);
                } else {
                    $('#selectedFileName').text('No image selected');
                }

                // Populate other form fields as needed

                // Open the edit modal
                $('#editProductModal').modal('show'); // This line initializes and shows the modal
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    

    // Submit the form when the "Save Changes" button is clicked
    $('#editProductForm').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Serialize the form data
        var formData = new FormData(this);

        // Send an AJAX request to update the product data
        $.ajax({
            type: 'POST',
            url: '{{ route("product.update") }}',
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting the content type
            success: function(response) {
                // Handle the success response, e.g., show a success message
                console.log('Product updated successfully:', response);
                
                // Optionally, you can close the modal or perform other actions here
                $('#editProductModal').modal('hide');
            },
            error: function(xhr, status, error) {
                // Handle any errors that occur during the update
                console.error('Error updating product:', xhr.responseText);
            }
        });
    });

    // ... Your displayProducts function remains the same

    function displayProducts(products) {
        var productList = $('#product-list');

        productList.empty();

        $.each(products, function(index, product) {
            // Create a new table row and populate it with data
            var productRow = $('<tr>');
            productRow.append($('<th scope="row">').text(index + 1));
            productRow.append($('<td>').text(product.prod_name));
            productRow.append($('<td>').text(product.unit));
            productRow.append($('<td>').text(product.price));
            productRow.append($('<td>').text(product.expiration_date));
            productRow.append($('<td>').text(product.available));
            var imageCell = $('<td>');
            if (product.image) {
                var productImage = $('<img>').attr('src', product.image).attr('alt', 'product image');
                imageCell.append(productImage);
            } else {
                imageCell.text('No image available');
            }
            productRow.append(imageCell);

            productList.append(productRow);
        });
    }
});

</script>

<script>
    // Delete button click event
    $('.delete-product').click(function() {
        var productId = $(this).data('product-id');

        // Confirm the deletion with the user (optional)
        if (confirm('Are you sure you want to delete this product?')) {
            // Send an AJAX request to delete the product
            $.ajax({
                type: 'DELETE',
                url: '{{ route("product.destroy", ":id") }}'.replace(':id', productId),
                success: function(response) {
                    // Handle the success response, e.g., show a success message
                    console.log('Product deleted successfully:', response);
                    // Optionally, you can reload the product list or perform other actions here
                },
                error: function(xhr, status, error) {
                    // Handle any errors that occur during the deletion
                    console.error('Error deleting product:', xhr.responseText);
                }
            });
        }
    });
</script>

@endsection
