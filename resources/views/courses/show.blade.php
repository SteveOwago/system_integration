@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
@endsection
@section('content')
    <div class="row">
        <div class="col-md-7 mx-auto mt-4">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <h6 class="mb-0">Course Information: {{ $course->name }}</h6>
                </div>
                <div class="card-body pt-4 p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">Course Description</h6>
                                <span class="mb-2 text-xs">{{ $course->description }} </span>
                                <h6 class="mb-3 text-sm">Course Prize</h6>
                                <span class="mb-2 text-xs">KES. {{ number_format($course->price, 2) }} </span>
                            </div>

                        </li>
                        <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">Payment Status</h6>
                                <span class="mb-2 text-xs text-danger">Not Paid</span>
                            </div>
                            <div class="ms-auto text-end">

                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-outline-primary px-3 mb-0" data-toggle="modal"
                                    data-target="#staticBackdrop">
                                    Pay Now
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false"
                                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel">Amount: KES
                                                    {{ number_format($course->price) }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('courses.payment', $course->id) }}" method="post">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group text-center">
                                                        <label for="">Enter Mpesa Phone Number</label>
                                                        <input class="form-control" type="number" name="phone"
                                                            placeholder="07XXXXXXXX">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Pay</button>
                                                </div>


                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
@endsection
