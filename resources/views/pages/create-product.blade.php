@extends('layouts.app')
@section('content')

<h2 align="center"><b>Product Form</b></h2>
<div class="error"></div>

<form id="product-form" enctype="multipart/form-data">
    @csrf
{{-- <input type="text"  name="prod_name">
<input type="text" name="unit">
<input type="number" name="price" step="any">
<input type="date" name="expiration_date" step="any">
<input type="number" name="available">
<input type="file" name="image"> --}}
<div class="container-md">
    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon1">Product Name</span>
        <input type="text" class="form-control" aria-label="productName" aria-describedby="basic-addon1" name="prod_name" required minlength="4" maxlength="20">
    </div>
    
    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon2">Unit</span>
        <input type="text" class="form-control" aria-label="Unit" aria-describedby="basic-addon2" name="unit" required minlength="4" maxlength="20">
    </div>
    
    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon3">Price</span>
        <input type="number" class="form-control" aria-label="Price" aria-describedby="basic-addon3" name="price" step="any" required minlength="1" maxlength="10">
    </div>
    
    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon4">Expiration Date</span>
        <input type="date" class="form-control" aria-label="ExpirationDate" aria-describedby="basic-addon4" name="expiration_date" step="any" required>
    </div>
    
    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon5">Available Product</span>
        <input type="number" class="form-control" aria-label="AvailableProduct" aria-describedby="basic-addon5" name="available" required minlength="1" maxlength="10">
    </div>
    
    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon6">Image</span>
        <input type="file" class="form-control" aria-label="ProductImage" aria-describedby="basic-addon6" name="image" required accept=".jpg, .jpeg, .png">
    </div>
    <div style="float: right">
        <a href="/show-product" class="btn btn-primary">Show Product</a>
        <button class="btn btn-success" type="submit">Submit</button>
    </div>
   
</div>
  
</form>

<script>
    $(document).ready(function() {
        $('#product-form').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: '{{ route("product.store") }}',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                     // Clear the error messages
                     $('.error').remove();
                    alert(response.message);
                    // You can perform additional actions here, such as clearing the form or redirecting
                },
                error: function(xhr, status, error) {
                    var jsonError = JSON.parse(xhr.responseText);
                    displayErrors(jsonError.errors);
                }
            });
        });
        // Function to display validation errors
        function displayErrors(errors) {
            var errorHtml = '<div class="alert alert-danger" style="text-align: center">';
            $.each(errors, function (key, value) {
                errorHtml += '<p>' + value + '</p>';
            });
            errorHtml += '</div>';
            
            $('.error').html(errorHtml);
        }
    });
</script>


@endsection
