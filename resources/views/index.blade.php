<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Register</title>
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <section class="contact-from pt-4">
        <div class="container">
            <div class="row mt-5">
                <div class="col-md-7 mx-auto">
                    <div class="form-wrapper">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>User form</h4>
                            </div>
                        </div>
                        {{-- <form action="{{url('/')}}/user/add" method="post" enctype="multipart/form-data" id="userForm"> --}}
                        <form method="post" enctype="multipart/form-data" id="userForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" class="form-control" value="{{old('name')}}" placeholder="Name">
                                        <span class="text-danger" id="nameError"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" class="form-control" value="{{old('email')}}" placeholder="Email">
                                        <span class="text-danger" id="emailError"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{old('phone_number')}}" placeholder="Phone number">
                                        <span class="text-danger" id="phoneError"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea name="description" id="description" cols="30" rows="3" class="form-control" value="{{old('description')}}" placeholder="Description"></textarea>
                                        <span class="text-danger" id="descriptionError"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <select name="user_role" class="custom-select" id="user_role">
                                            <option value=''>Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->role }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" id="roleError"></span>

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="file" name="profile_image" id="profile_image" class="form-control" >
                                        <span>jpeg/ jpg/ png/ svg</span>
                                        <span class="text-danger" id="imageError"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="reset" class="btn btn-primary">Reset</button>
                                <input type="submit" id="addStatButton" class="btn btn-primary submit" value="Register"/>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container" style="margin-top:100px">
            <h4>Users List</h4>
            <table class="table table-striped" id="user-table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone number</th>
                    <th scope="col">Description</th>
                    <th scope="col">Role</th>
                    <th scope="col">Profile Image</th>
                  </tr>
                </thead>
                <tbody id="recordsTable">
                </tbody>
              </table>
            </div>
    </section>
</body>
</html>

<script>
    
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        loadUpdatedData();
        $('#userForm').on('submit', function (event) {
            event.preventDefault();
            $('.error').text('');
            var regex = /^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[0]?)?[789]\d{9}$/;
            const phone = document.getElementById('phone_number').value;
            var allowedExtension = ['jpeg', 'jpg', 'gif'];
            

            // Perform client-side validation
            let valid = true;
            if ($('#name').val() === '') {
                $('#nameError').text('Name is required.');
                valid = false;
            }
            if ($('#email').val() === '') {
                $('#emailError').text('Email is required.');
                valid = false;
            }
            if ($('#phone_number').val() === '') {
                $('#phoneError').text('Phone number is required.');
                valid = false;
            }  else if(!($.isNumeric($("#phone_number").val()))) {
                $('#phoneError').text('cannot contain letters.');
                valid = false;
            } else if($('#phone_number').val().length !== 10){
                $('#phoneError').text('Must be 10 Digits.');
                valid = false;
            }
            else if(!regex.test(phone)){
                $('#phoneError').text('Invalid Mobile Numbers.');
                valid = false;
            }

            if ($('#description').val() === '') {
                $('#descriptionError').text('Description is required.');
                valid = false;
            }
            if ($('#user_role').val() === '') {
                $('#roleError').text('User role is required.');
                valid = false;
            }

            if (valid) {
                $.ajax({
                    enctype: 'multipart/form-data',
                    url: "{{ route('form.submit') }}",
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    data: new FormData(this),
                    // data: $(this).serialize(),
                    success: function (response) {
                        resetForm('userForm');
                        loadUpdatedData();
                        $('#response').html('<p>' + response.message + '</p>');
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += '<p>' + errors[field][0] + '</p>';
                        }
                        $('#response').html(errorMessages);
                    }
                });
            }
        });

        // reset the form data
        function resetForm(formid) {
        $(':input','#'+formid) .not(':button, :submit, :reset, :hidden') .val('')
        .removeAttr('checked') .removeAttr('selected');
        $('#' + formid).find('span').text('');
        }

        // fetch the updated data
        function loadUpdatedData() {
        $.ajax({
            url: "{{ route('fetch.data') }}",
            method: 'GET',
            dataType: 'json',
            success: function (response) {

            console.log('response'+response);

            $("tbody").empty();
                var markup = '';
                var inc = 1;
                $.each(response.user, function(key, value) {
                    var path = '{{asset(Storage::url('/'))}}';
                    var roleName = value.role ? value.role : 'No Role';
                    markup += '<tr>' +
                    '<td>' + inc + '</td>' +
                    '<td>' + value.name + '</td>' +
                    '<td>' + value.email + '</td>' +
                    '<td>' + value.phone + '</td>' +
                    '<td>' + value.description + '</td>' +
                    '<td>' + value.roles.role + '</td>' +
                    '<td><img src="' +path+ '/' +value.profile_image + '" alt="NA" style="width: 100px; height: auto;"></td>' +
                    '</tr>';
                     inc++;
                });
                $("tbody").append(markup);
                },
                error: function(xhr) {
                console.error('Error fetching data:', xhr);
                }
        });
    }

    });

    
</script>