@extends('layouts.app')
@section('content')
    <div class="mt-16">
        <div class="row">
            @foreach($products as $item)
                <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card border-0 rounded-0 text-center shadow-none overflow-hidden">
                            <a href="#!">
                                <span class="badge badge-primary">
                                </span>
                                <img src="{{$item->img_thumb}}" alt="" class="card-img-top rounded-0">
                                <div class="card-body">
                                    <h4 class="text-uppercase mb-3">smart watch</h4>
                                    <p class="h4 text-muted font-weight-light mb-3">Lip Gloss</p>
                                    <p class="h4">$25.00</p>
                                </div>
                            </a>
                        </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection
