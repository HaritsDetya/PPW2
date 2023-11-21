@extends('auth.layouts')
@section('content')

<body>
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <form action="{{ route('gallery.update', $movie->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">New Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" name="name" value="{{ old('title', $movie->title) }}" placeholder="Insert New Title">
                                @error('title')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">New Description</label>
                                <input type="text" class="form-control @error('content') is-invalid @enderror" name="description" rows="5" value="{{ old('description', $movie->description) }}" placeholder="Insert New Description"></input>
                                @error('content')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <label class="font-weight-bold">Image</label>
                            <div class="form-group mb-3">
                                @if($movie->photo)
                                <img src="{{asset('storage/'.$movie->photo )}}" class="img-preview img-fluid mb-3" width="250px">
                                @else
                                <img class="img-preview img-fluid mb-3 col-sm-5">
                                @endif
                                <input type="file" class="form-control" name="photo" id="photo" onchange="previewImage()">
                            </div>
                            @error('image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <button type="submit" class="btn btn-md btn-primary">UPDATE</button>
                            <button type="reset" class="btn btn-md btn-warning">RESET</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection