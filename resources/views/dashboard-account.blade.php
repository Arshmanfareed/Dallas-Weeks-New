@extends('partials/master')
@section('content')
    <style>
        #payment-form input.form-control {
            color: white !important;
        }
    </style>
    @if (!empty($user->token))
        {!! '<p>Connacted.</p>' !!}
    @endif
    <section class="dashboard">
        <div class="container-fluid">
            <div class="row">
                @include('partials/dashboard_sidebar')
                <div class="col-lg-8">
                    <div class="dashboard_cont">
                        <div class="row_filter d-flex align-items-center justify-content-between">
                            <div class="account d-flex align-items-center">
                                @php
                                    $user = auth()->user();
                                @endphp
                                <img src="{{ asset('assets/img/account_img.png') }}"
                                    alt=""><span>{{ $user->name }}</span>
                            </div>
                            <div class="form_add d-flex">
                                <form action="/search" method="get" class="search-form">
                                    <input type="text" name="q" placeholder="Search...">
                                    <button type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </form>
                                <div class="add_btn">
                                    <a href="javascript:;" type="button" data-bs-toggle="modal"
                                        data-bs-target="#addaccount"><i class="fa-solid fa-plus"></i></a>Add account
                                </div>
                                
                            </div>
                        </div>
                        <hr>
                        <div class="row_table">
                            <div class="add_account_div">
                                <img src="{{ asset('assets/img/empty.png') }}" alt="">
                                <p class="text-center">You don't hanve any account yet. Start by adding your first account.
                                </p>
                                @if (auth()->check() && auth()->user()->id == $user->id && $paymentStatus == 'success' && empty($user->account_id))
                                    <input type="hidden" id="user_email" value="{{ $user->email }}">
                                    <button id="submit-btn" type="button" class="theme_btn mb-3">Connect Linked in</button>
                                @elseif (!empty($user->account_id))
                                    <input type="hidden" id="user_email" value="{{ $user->email }}">
                                    <button style="background-color: #16adcb;" id="submit-btn" type="button" class="theme_btn mb-3">Change Linked in Account</button>
                                @endif
                                <div class="add_btn">
                                    <a href="javascript:;" type="button" data-bs-toggle="modal"
                                        data-bs-target="#addaccount"><i class="fa-solid fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (Session::has('success'))
        <div class="alert alert-success text-center">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <p>{{ Session::get('success') }}</p>
        </div>
    @endif
    <!-- basic modal -->
    <div class="modal fade step_form_popup" id="addaccount" tabindex="-1" role="dialog" aria-labelledby="addaccount"
        aria-hidden="true">
        <div class="modal-dialog" style="border-radius: 45px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="text-center">Add Account</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <!-- Add Account Popup -->
                    <form role="form" action="{{ route('stripe.post') }}" method="post" data-cc-on-file="false"
                        data-stripe-publishable-key="pk_test_51KQb3pC6mJiJ0AUpeAjoS786h11qy1jW92S6gWsGD4NpK4JGOuKplhC2I0vHFgEWwRy7T9NwHDZPiILuzQPynCdK007sgX6ox6" method="post"
                        class="form step_form require-validation" id="payment-form">
                        @csrf
                        <!-- Progress Bar -->
                        <div class="progress-bar">
                            <div class="progress" id="progress"></div>
                            <div class="progress-step active" data-title="Seat"></div>
                            <div class="progress-step" data-title="Add account"></div>
                            <div class="progress-step" data-title="Company "></div>
                            <div class="progress-step" data-title="Account info"></div>
                            <div class="progress-step" data-title="Payment"></div>
                            <!-- <div class="progress-step" data-title="Integration"></div> -->
                        </div>
                        <!-- Steps -->
                        <div class="form-step active">
                            <h3>Select Seat</h3>
                            <div class="form_row row">
                                <div class="input-group col-12">
                                    <label for="address">Seat</label>
                                    <select name="seat" id="">
                                        <option value="seat">Seat</option>
                                        <option value="seat">Seat</option>
                                        <option value="seat">Seat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-next">Next</a>
                            </div>
                        </div>
                        <div class="form-step ">
                            <h3>Personal Informations</h3>
                            <div class="form_row row">
                                <div class="input-group col-12">
                                    <label for="username">User Name</label>
                                    <input type="text" name="username" id="username" placeholder="User Name">
                                </div>
                                <div class="input-group col-6">
                                    <label for="City">City</label>
                                    <input type="text" name="city" id="City" placeholder="Enter your city">
                                </div>
                                <div class="input-group col-6">
                                    <label for="State">State</label>
                                    <input type="text" name="state" id="State" placeholder="Enter your state">
                                </div>
                                <div class="input-group col-12">
                                    <label for="Company">Company name</label>
                                    <input type="text" name="company" id="Company"
                                        placeholder="Enter your company name">
                                </div>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-prev">Previous</a>
                                <a class="btn btn-next">Next</a>
                            </div>
                        </div>
                        <div class="form-step ">
                            <h3>Contact Informations</h3>
                            <div class="input-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email">
                            </div>
                            <div class="input-group">
                                <label for="phone">Phone Number</label>
                                <input type="phone" name="phone" id="phone">
                            </div>
                            <div class="input-group">
                                <label for="summary">Profile Summary</label>
                                <textarea name="summary" id="summary" cols="42" rows="6"></textarea>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-prev">Previous</a>
                                <a class="btn btn-next">Next</a>
                            </div>
                        </div>
                        <div class="form-step ">
                            <!-- <h3>Connect Linkedin</h3> -->
                            <!-- <div class="col-md-6">
                                <label for="linkedin">User Email</label>
                                <input id="linkedin" class="linkedin-email" name="email" type="email" placeholder="Enter Your Email" />
                                <label for="Password">Password</label>
                                <input id="password" name="password" type="password" placeholder="Enter Password" />
                                <button id="submit-btn" type="button" class="">Submit</button>
                            </div>   -->

                            <!-- <a href="{{ URL('auth/linkedin/redirect') }}">Login Via LinkedIn</a> -->
                            <h3>Social Links</h3>
                            <div class="input-group">
                                <label for="linkedin">LinkedIn</label>
                                <div class="input-box">
                                    <span class="prefix">linkedin.com/in/</span>
                                    <input id="linkedin" name="linkedin" type="text" placeholder="USER123" />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="twitter">Twitter</label>
                                <div class="input-box">
                                    <span class="prefix">twitter.com/</span>
                                    <input id="twitter" name="twitter" type="text" placeholder="USER123" />
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="github">Github</label>
                                <div class="input-box">
                                    <span class="prefix">github.com/</span>
                                    <input id="github" name="github" type="text" placeholder="USER123" />
                                </div>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-prev">Previous</a>
                                <a class="btn btn-next">Next</a>
                            </div>
                        </div>
                        <div class="form-step ">
                            <h3>Payment</h3>
                            <div class="experiences-group">
                                <div class='form-row row'>
                                    <div class='col-xs-12 form-group required'>
                                        <label class='control-label'>Name on Card</label>
                                        <input class='form-control' size='4' type='text'>
                                    </div>
                                </div>
                                <div class='form-row row'>
                                    <div class='col-xs-12 form-group  required'>
                                        <label class='control-label'>Card Number</label>
                                        <input autocomplete='off' class='form-control card-number' size='20'
                                            type='text'>
                                    </div>
                                </div>
                                <div class='form-row row'>
                                    <div class='col-xs-12 col-md-4 form-group cvc required'>
                                        <label class='control-label'>CVC</label>
                                        <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311'
                                            size='4' type='text'>
                                    </div>
                                    <div class='col-xs-12 col-md-4 form-group expiration required'>
                                        <label class='control-label'>Expiration Month</label> <input
                                            class='form-control card-expiry-month' placeholder='MM' size='2'
                                            type='text'>
                                    </div>
                                    <div class='col-xs-12 col-md-4 form-group expiration required'>
                                        <label class='control-label'>Expiration Year</label>
                                        <input class='form-control card-expiry-year' placeholder='YYYY' size='4'
                                            type='text'>
                                    </div>
                                </div>
                                <!-- <div class='form-row'>
                                            <div class='col-md-12 error form-group hide'>
                                                <div class='alert-danger alert'>Please correct the errors and try again.</div>
                                            </div>
                                        </div>  -->
                            </div>
                            <!--  <div class="add-experience">
                                                                                                                                                                                                                                            <a class="add-exp-btn"> + Add Experience</a>
                                                                                                                                                                                                                                        </div> -->
                            <div class="btn-group">
                                <a class="btn btn-prev">Previous</a>
                                <button class="btn btn-primary btn-lg btn-block" type="submit">Pay Now</button>
                                <!-- <input type="submit" value="Complete" name="complete" class="btn btn-complete"> -->

                            </div>
                        </div>
                    </form>
                </div>
                <!-- <div class="modal-footer">
                                                                                                                                                                                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                                                                                                                                                                                <button type="button" class="btn btn-primary">Save changes</button>
                                                                                                                                                                                                                            </div> -->
            </div>
        </div>
    </div>

    <!-- <a href="javascript:;" id="linkedin-auth-btn">Authenticate with LinkedIn</a> -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        jQuery(document).ready(function() {
            // Attach a click event to the LinkedIn authentication button
            jQuery('#linkedin-auth-btn').click(function(e) {
                e.preventDefault();
                // Perform the AJAX request to the LinkedIn authentication route
                jQuery.ajax({
                    type: 'GET',
                    url: '/auth/linkedin/redirect', // Change this URL to your actual route
                    success: function(response) {
                        // Handle the success response if needed
                        console.log(response);
                        // After successful authentication, you can redirect to the callback route
                        window.location.href = '/auth/linkedin/callback';
                    },
                    error: function(error) {
                        // Handle the error response if needed
                        console.error(error);
                    }
                });
            });
        });
    </script>

    <script>
        $('.btn-next').on('click', function(e) {
            var progress_step = $('.progress-step.active');
            var form_step = $('.form-step.active');
            $(form_step).removeClass('active');
            $(progress_step).removeClass('active');
            $(form_step).next('.form-step').addClass('active');
            $(progress_step).next('.progress-step').addClass('active');
            $('#progress').css({
                'width': $('.progress-step.active').position().left + 170,
            });
        });
        $('.btn-prev').on('click', function(e) {
            var progress_step = $('.progress-step.active');
            var form_step = $('.form-step.active');
            $(form_step).removeClass('active');
            $(progress_step).removeClass('active');
            $(form_step).prev('.form-step').addClass('active');
            $(progress_step).prev('.progress-step').addClass('active');
            console.log($('.progress-step.active').position().left);
            console.log($('.progress-step.active').next('.progress-step').position().left);
            $('#progress').css({
                'width': $('.progress-step.active').position().left + 170,
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#payment-form').on('submit', function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('stripe.post') }}',
                    data: formData,
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(error) {
                        console.log(error);
                    },
                });
            });
        });
    </script>
    
    <script>
        $(document).ready(function() {
            $('#submit-btn').on('click', function() {

                $.ajax({
                    url: '/api/create-link-account',
                    type: 'POST',
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        'email': $('#user_email').val()
                    },
                    success: function(response) {
                        console.log(response);

                        if (response.status === 'success' && response.data && response.data
                            .url) {
                            console.log(response.data);
                            console.log(response.data.url);
                            window.open(response.data.url, '_blank');
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>

@endsection
