<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/fav.png') }}">
    <title></title>

    <!-- Favicon-->


    <!-- Google Icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Bootstrap Core CSS -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css"  />
    <style>
        .iti {
            width: 100%;
        }
    </style>

    @vite(['resources/sass/main.scss'])

</head>

<body id="page-top">

    <header></header>
    <!-- Header Section Ends -->
    @yield('content')

    <section class="d5-bg">
        <div class="px-5 p-t-24 p-b-24">
            <div class="d-flex justify-content-center footer">
                <p class="d3 f-14" style="margin-left:auto;">Copyright © <?php echo date('Y'); ?> PMC</p>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script>
    $(document).ready(function(){

 // Initialize select2
    $(".SelExample").select2();
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    });
  </script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">


    <script>

        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
        });

        $(document).ready(function() {
            $('#searchtbl').DataTable({
                "order": [],
                "language": {
                "paginate": {
                "previous": "<",
                "next": ">"
                }
            },
            "bLengthChange" : false,
            columnDefs: [
                { orderable: false, targets: -1 }
            ],
            });

            $('#search').keyup(function() {
                var table = $('#searchtbl').DataTable();
                table.search($(this).val()).draw();
            });
        });

        //Image upload preview function
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#uploadimage').attr('src', e.target.result)
                    $('#uploadimage').parent().addClass('uploadedimg').removeClass('serviceimg');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        // When Password eye Icon Click
        function togglePasswordVisibility() {
            var passwordField = document.getElementById('password');
            var icon = document.getElementById('password_img');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordField.type = "password";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        document.getElementById("numericInput").addEventListener("input", function(event) {
            // Get the input value
            let input = event.target.value;

            // Remove any non-numeric characters using regular expression
            input = input.replace(/\D/g, '');

            // Update the input value
            event.target.value = input;
        });

    </script>

    @stack('js')
</body>

</html>
